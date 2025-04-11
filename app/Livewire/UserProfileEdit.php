<?php

namespace App\Livewire;

use App\Http\Controllers\BiostarLoginDetails;
use App\Models\bs2users;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;


class UserProfileEdit extends Component
{

    #[Rule('required|string')]
    public $user_id;
    #[Rule('required|string')]
    public $userName = "";
    public $fingerprintImage;

    public $userGroupId = '1';

    public $apikey = [];

    public $fingerQualitys = [100, 80, 60, 40, 20];

    public $selectFingerQuality = 80;
    public $profilepic = "";
    public $template = "";
    public $scanfinger = [];
    public $sessionId;
    public $name = "";

    public $apiUrl;
    public $cardnumber = 0;


    public $selectDevice;

    public $userGroups;

    public $imageContent;

    use WithFileUploads;

    public $image;
    public $message;
    public $success;
    public $visualfaceadd = false;
    public $profilePicselected;
    public $visualfaceselected;

    public $devicelist = [];

    public $fingerdevicelist = [
        'BioStation 2',
        'X-Station 2',
        'BioEntry W2',
        'BioEntry P2',
        'BioEntry R2',
        'BioLite N2',
        'FaceStation F2',
        'BioStation 2a',
        'BioStation L2',
    ];

    public $carddevicelist = [
        'BioStation 2',
        'X-Station 2',
        'BioEntry W2',
        'BioEntry P2',
        'BioEntry R2',
        'BioLite N2',
        'FaceStation F2',
        'BioStation 2a',
        'BioStation L2',
        'XPass 2',
        'XPass S2',
        'XPass D2',
    ];

    public $profileImage;

    public $facedevicelist = [
        'FaceStation F2',
        'BioStation 3',

    ];

    public function mount()
    {
        $biostarLoginDetails = new BiostarLoginDetails();

        $this->sessionId = $biostarLoginDetails->sessionId;
        $this->apiUrl = $biostarLoginDetails->apiUrl;


        $userGrouplist = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->get($this->apiUrl . '/user_groups');


        $this->userGroups = $userGrouplist->json()['UserGroupCollection'];

        $devicelists = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->get($this->apiUrl . '/devices');

        $devicelists->json()['DeviceCollection'];
        foreach ($devicelists->json()['DeviceCollection']['rows'] as $device) {
            // dd($device);
            $this->devicelist[] = [
                'name' => $device['name'],
                'device_id' => $device['id'],
                'devicetypename' => $device['device_type_id']['name'],
                'devicetype' => in_array($device['device_type_id']['name'], $this->fingerdevicelist) ? 'Fingerprint' :
                    (in_array($device['device_type_id']['name'], $this->carddevicelist) ? 'Card' :
                        (in_array($device['device_type_id']['name'], $this->facedevicelist) ? 'Face' : 'Other'))
            ];
        }

        $userDetails = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->get($this->apiUrl . '/users/' . $this->user_id);
        //dd($this->image = $userDetails->json()['User']['photo_exists']);
        if($userDetails->json()['User']['photo_exists']=='true'){

            $this->profileImage = $userDetails->json()['User']['photo'];
        }
        $this->userName = $userDetails->json()['User']['name'];
        $this->userGroupId = $userDetails->json()['User']['user_group_id']['id'];

        if ($userDetails->json()['User']['fingerprint_template_count'] > 0) {

            if ($userDetails->json()['User']['fingerprint_template_count'] == 1) {
                $this->scanfinger[] = [
                    [
                        'temp1' => $userDetails->json()['User']['fingerprint_templates'][0]['template0'],
                        'temp1image' => $userDetails->json()['User']['fingerprint_templates'][0]['template_image0']
                    ]
                ];

            } elseif ($userDetails->json()['User']['fingerprint_template_count'] == 2) {
                $this->scanfinger = [
                    [
                        'temp1' => $userDetails->json()['User']['fingerprint_templates'][0]['template0'],
                        'temp1image' => $userDetails->json()['User']['fingerprint_templates'][0]['template_image0']
                    ],
                    [
                        'temp1' => $userDetails->json()['User']['fingerprint_templates'][1]['template0'],
                        'temp1image' => $userDetails->json()['User']['fingerprint_templates'][1]['template_image0']
                    ]
                ];

            }

        }



    }

    public function updatevisualfaceselected($value)
    {

        $this->visualfaceselected = $value;
    }

    public function updateuserGroupId($value)
    {

        $this->userGroupId = $value;
    }
    public function updateselectDevice($value)
    {

        $this->selectDevice = $value;
    }

    public function deleteFinger($index)
    {


        unset($this->scanfinger[$index]);

        $this->scanfinger = array_values($this->scanfinger);
    }


    public function scan()
    {
        if ($this->selectDevice == null) {
            session()->flash('deviceselected', "Please select a device");
            return;
        }
        $response = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->post($this->apiUrl . '/devices/' . $this->selectDevice . '/scan_fingerprint', [
                "ScanFingerprintOption" => [
                    "enroll_quality" => $this->selectFingerQuality,
                    "raw_image" => true
                ]
            ]);
//dd($response->json()['FingerprintTemplate']['template_image0']);
        if ($response->json()['DeviceResponse']['result'] == 'false') {

            session()->flash('error', "Please scan again");
        } else {

            //dd($response->json());
            $this->fingerprintImage = $response->json()['FingerprintTemplate']['template_image0'];

            $this->template = $response->json()['FingerprintTemplate']['template0'];

            $this->scanfinger[] = [
                [
                    'temp1' => $this->template,
                    'temp1image' => $this->fingerprintImage
                ]
            ];

        }

//dd($this->scanfinger);
    }

    public function scanVisualFace($user_id)
    {

        $this->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:10240', // Max 10MB, only JPG, JPEG, PNG
        ]);

        // Convert image to Base64
        $this->imageContent = base64_encode(file_get_contents($this->image->getRealPath()));

        $response = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->put($this->apiUrl . '/users/check/upload_picture', [
                'template_ex_picture' => $this->imageContent,
            ]);

        // dd($this->imageContent);
        if ($response->successful()) {


            $this->success = true;
            $this->message = '✅ Image meets the required specifications!';
            $this->visualfaceadd = true;
        } else {
            $this->success = false;
            $this->message = '❌ Image does not meet the specifications!';
        }





    }




    public function createUser()
    {

        if($this->image){
            if( $this->profilePicselected == 2){

                $this->profilepic=$this->imageContent;
            }
        }


        $response = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->put($this->apiUrl . '/users/' . $this->user_id, [
                'User' => [

                    'user_group_id' => ['id' => $this->userGroupId], // ✅ FIXED: Changed from array to integer
                    'start_datetime' => '2001-01-01T00:00:00.00Z',
                    'expiry_datetime' => '2030-12-31T23:59:00.00Z',
                    'disabled' => false,
                    'name' => $this->userName,
                    // 'email' => 'testuser@example.com',
                    // 'department' => '',
                    // 'user_title' => '',
                    'photo' => $this->profilepic,
                    // 'phone' => '010-1111-2222',
                    // 'permission' => 1,  // ✅ FIXED: Ensure it's a number
                    // 'access_groups' => [1], // ✅ FIXED: Use array of IDs
                    // 'login_id' => '',
                    // 'password' => '',
                    // 'user_ip' => '127.0.0.1',
                ],
            ]);

        if ($this->image) {

            $validate = $this->validate([

                'image' => 'nullable|image|sometimes|max:10240',
            ]);

            if ($this->image) {
                $validate['image'] = $this->image->store('uploads', 'public');

            }

            $userdetails = bs2users::all()->where('userid', $this->user_id)->first();

            if ($userdetails == null) {

                bs2users::create([
                    'userid' => $this->user_id,
                    'name' => $this->userName,
                    'image' => $validate['image']
                ]);

            } else {

                if ($userdetails->userid == $this->user_id) {

                    bs2users::where('userid', $this->user_id)->update([
                        'name' => $this->userName,
                        'image' => $validate['image']
                    ]);

                } else {

                    bs2users::create([
                        'userid' => $this->user_id,
                        'name' => $this->userName,
                        'image' => $validate['image']
                    ]);
                }
            }



        }
//dd($this->scanfinger);

        if ($this->visualfaceselected == 1) {

            $addcsncard = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->put($this->apiUrl . '/users/' . $this->user_id, [
                    "User" => [
                        "credentials" => [
                            "visualFaces" => [
                                [
                                    "template_ex_picture" => $this->imageContent
                                ]
                            ]
                        ]
                    ]
                ]);
        }

        if (empty($this->scanfinger)) {



            $fingerscan = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->put($this->apiUrl . '/users/' . $this->user_id, [
                    "User" => [
                        "fingerprint_templates" => [
                        ]
                    ]

                ]);





        } else {
            if (count($this->scanfinger) == 1) {

                $fingerscan = Http::withoutVerifying()
                    ->withHeaders([
                        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                        'accept' => 'application/json',
                    ])
                    ->put($this->apiUrl . '/users/' . $this->user_id, [
                        "User" => [
                            "fingerprint_templates" => [
                                [
                                    "finger_mask" => "false",
                                    "template0" => $this->scanfinger[0][0]['temp1'],
                                    "template1" => $this->scanfinger[0][0]['temp1']
                                ]


                            ]
                        ]
                    ]);

            } elseif (count($this->scanfinger) == 2) {
                $fingerscan = Http::withoutVerifying()
                    ->withHeaders([
                        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                        'accept' => 'application/json',
                    ])
                    ->put($this->apiUrl . '/users/' . $this->user_id, [
                        "User" => [
                            "fingerprint_templates" => [
                                [
                                    "finger_mask" => "false",
                                    "template0" => $this->scanfinger[0][0]['temp1'],
                                    "template1" => $this->scanfinger[0][0]['temp1']
                                ],
                                [
                                    "finger_mask" => "false",
                                    "template0" => $this->scanfinger[1][0]['temp1'],
                                    "template1" => $this->scanfinger[1][0]['temp1']
                                ],


                            ]
                        ]
                    ]);
            }
        }

        //     if($this->image !=""){

        //         $validate = $this->validate([

        //             'image' => 'nullable|image|sometimes|max:10240',
//         ]);

        //         if ($this->image) {
//             $validate['image']= $this->image->store('uploads', 'public');

        //         }
// ///dd($this->userName);
//         bs2users::create([
//             'userid' => $this->userID,
//             'name' => $this->userName,
//             'image' => $validate['image']
//         ]);
//     }

        //     if($this->cardnumber){

        //         $getAllCards = Http::withoutVerifying()
//     ->withHeaders([
//         'bs-session-id' => $this->sessionId, // Pass session ID for authentication
//         'accept' => 'application/json',
//     ])
//     ->get($this->apiUrl . '/cards');
// //dd($getAllCards->json()['CardCollection']['total']);

        //     for($i=0; $i < $getAllCards->json()['CardCollection']['total']; $i++){

        //         if($getAllCards->json()['CardCollection']['rows'][$i]['card_id']==$this->cardnumber){

        //             if($getAllCards->json()['CardCollection']['rows'][$i]['is_assigned']=='false'){

        //                 $assigncard = Http::withoutVerifying()
//                 ->withHeaders([
//                     'bs-session-id' => $this->sessionId, // Pass session ID for authentication
//                     'accept' => 'application/json',
//                 ])

        //                 ->put($this->apiUrl . '/users/'.$this->userID,[
//                     'User' => [
//                         'cards' => [
//                             ['id' => $getAllCards->json()['CardCollection']['rows'][$i]['id']]
//                         ]
//                     ]

        //                 ]);

        //             session()->flash('success', 'Card is successfully assigned.');
//             }else{
//                 session()->flash('error', 'Card is already assigned to a user.');
//             }


        //         }else{
//             session()->flash('error', 'Card is not found. Please first add card to the system.');
//         }
//     }

        //       }

        if ($response->successful()) {




            session()->flash('success', 'User Updated successfully.');

            return redirect()->route('items');

        } else {
            session()->flash('error', 'User Updated not successfully.');
        }

    }




    public function cancel()
    {

        return redirect()->route('items');
    }

    public function render()
    {

        return view('livewire.user-profile-edit');
    }
}
