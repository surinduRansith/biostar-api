<?php

namespace App\Livewire;

use App\Http\Controllers\BiostarLoginDetails;
use App\Models\bs2users;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserRegisterForm extends Component
{
    #[Rule('required|string')]
    public $userID;
    #[Rule('required|string')]
    public $userName="";
    public $fingerprintImage;

    public $userGroupId='1';

    public $apikey = [];

    public $fingerQualitys = [100,80,60,40,20];

    public $selectFingerQuality=80;

    public $template = "";
    public $scanfinger = [];
    public $sessionId;
    public $name = "";
    public $user_id = "";
    public $apiUrl;
    public $cardnumber = 0;


    public $selectDevice;

    public $userGroups;

public $imageContent;

    use WithFileUploads;

    public $image;
    public $message;
    public $success;
    public $visualfaceadd=false;

    public $visualfaceselected;

    public $devicelist=[];

    public $profilePicselected;

    public $profilepic="";

    public $fingerdevicelist=[
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

public $carddevicelist=[
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

public $facedevicelist=[
    'FaceStation F2',
    'BioStation 3',

];

    



    public function mount()
    {
       
        
        $biostarLoginDetails = new BiostarLoginDetails();

        $this->sessionId = $biostarLoginDetails->sessionId;
        $this->apiUrl = $biostarLoginDetails->apiUrl;

        $response = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->get($this->apiUrl . '/users/next_user_id');


        $this->userID = $response->json()['User']['user_id'];

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
    }

    public function updatevisualfaceselected($value){

        $this->visualfaceselected = $value;
    }

    public function updateuserGroupId($value){

        $this->userGroupId = $value;
    }
    public function updateselectDevice($value){

        $this->selectDevice = $value;
    }

    public function updateprofilePicselected($value){
        $this->profilePicselected = $value;
    }

    public function updatedselectFingerQuality($value)
    {
        // Trigger when the value changes, and add it if it's valid
       
            $this->selectFingerQuality = $value; // Add the new value to the array
     
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
            ->post($this->apiUrl . '/devices/'.$this->selectDevice.'/scan_fingerprint', [
                "ScanFingerprintOption" => [
                    "enroll_quality" => $this->selectFingerQuality,
                    "raw_image" => true
                ]
            ]);

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


    }

    public function deleteFinger($index){
        
        unset($this->scanfinger[$index]);
        
        $this->scanfinger = array_values($this->scanfinger);
      
    }



    public function scanVisualFace($user_id){

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
                'template_ex_picture' =>  $this->imageContent,
            ]);

           // dd($this->imageContent);
           if ($response->successful()) {

           
            $this->success = true;
            $this->message = '✅ Image meets the required specifications!';
            $this->visualfaceadd=true;
        } else {
            $this->success = false;
            $this->message = '❌ Image does not meet the specifications!';
        }

        

      
      
    } 




    public function addCard($userID){

    if ($this->selectDevice == null) {
        session()->flash('error', "Please select a device");
        return;
    }
    
        $response = Http::withoutVerifying()
        ->withHeaders([
            'bs-session-id' => $this->sessionId, // Pass session ID for authentication
            'accept' => 'application/json',
        ])
        ->post($this->apiUrl . '/devices/'.$this->selectDevice.'/scan_card');
    
        $this->cardnumber=$response->json()['Card']['card_id'];
    
        $addcsncard = Http::withoutVerifying()
        ->withHeaders([
            'bs-session-id' => $this->sessionId, // Pass session ID for authentication
            'accept' => 'application/json',
        ])
        ->post($this->apiUrl . '/cards',[
            "CardCollection" => [
                "rows" => [
                    [
                        "card_id" => $this->cardnumber,
                        "card_type" => [
                            "id" => $response->json()['Card']['card_type']['id'],
                            "type" => $response->json()['Card']['card_type']['type']
                        ]
                    ]
                ]
            ]
        ]);
    
    
    
        $getAllCards = Http::withoutVerifying()
        ->withHeaders([
            'bs-session-id' => $this->sessionId, // Pass session ID for authentication
            'accept' => 'application/json',
        ])
        ->get($this->apiUrl . '/cards');
    //dd($getAllCards->json()['CardCollection']['total']);
    
        for($i=0; $i < $getAllCards->json()['CardCollection']['total']; $i++){
    
            if($getAllCards->json()['CardCollection']['rows'][$i]['card_id']==$this->cardnumber){
    
                if($getAllCards->json()['CardCollection']['rows'][$i]['is_assigned']=='false'){
                   
                    $assigncard = Http::withoutVerifying()
                    ->withHeaders([
                        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                        'accept' => 'application/json',
                    ])
                   
                    ->put($this->apiUrl . '/users/'.$userID,[
                        'User' => [
                            'cards' => [
                                ['id' => $getAllCards->json()['CardCollection']['rows'][$i]['id']]
                            ]
                        ]
                        
                    ]);
                   
                session()->flash('success', 'Card is successfully assigned.');
                }else{
                    session()->flash('error', 'Card is already assigned to a user.');
                }
    
                
            }else{
                session()->flash('error', 'Card is not found. Please first add card to the system.');
            }
            
    
        }
    
    }


    public function createUser(){
        
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
        ->post($this->apiUrl . '/users', [
            'User' => [
                'user_id' => $this->userID, 
                'user_group_id' => ['id' => $this->userGroupId], // ✅ FIXED: Changed from array to integer
                'start_datetime' => '2001-01-01T00:00:00.00Z',
                'expiry_datetime' => '2030-12-31T23:59:00.00Z',
                'disabled' => false,
                'name' =>   $this->userName,
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





        if($this->visualfaceselected==1){

            $addcsncard = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->put($this->apiUrl . '/users/'.$this->userID ,[
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

        if(!empty($this->scanfinger)){

           if(count($this->scanfinger)==1) {

            $response = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->put($this->apiUrl . '/users/'.$this->userID,[
                "User" => [
                    "fingerprint_templates" => [
                        [
                            "finger_mask" => "false",
                            "template0" => $this->scanfinger[0][0]['temp1'],
                            "template1" =>$this->scanfinger[0][0]['temp1']
                        ]
                       
                     
                    ]
                ]
            ]);

            }else{
                $response = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->put($this->apiUrl . '/users/'.$this->userID,[
                    "User" => [
                        "fingerprint_templates" => [
                            [
                                "finger_mask" => "false",
                                "template0" => $this->scanfinger[0][0]['temp1'],
                                "template1" =>$this->scanfinger[0][0]['temp1']
                            ],
                            [
                                "finger_mask" => "false",
                                "template0" => $this->scanfinger[1][0]['temp1'],
                                "template1" =>$this->scanfinger[1][0]['temp1']
                            ],
                           
                         
                        ]
                    ]
                ]);
            }

            
           
        }

        if ($this->image) {

            $validate = $this->validate([

                'image' => 'nullable|image|sometimes|max:10240',
            ]);

            if ($this->image) {
                $validate['image'] = $this->image->store('uploads', 'public');

            }

            $userdetails = bs2users::all()->where('userid', $this->userID)->first();

            if ($userdetails == null) {

                bs2users::create([
                    'userid' => $this->userID,
                    'name' => $this->userName,
                    'image' => $validate['image']
                ]);

            } else {

                if ($userdetails->userID == $this->userID) {

                    bs2users::where('userid', $this->userID)->update([
                        'name' => $this->userName,
                        'image' => $validate['image']
                    ]);

                } else {

                    bs2users::create([
                        'userid' => $this->userID,
                        'name' => $this->userName,
                        'image' => $validate['image']
                    ]);
                }
            }

        }
       
        if($this->cardnumber){

            $getAllCards = Http::withoutVerifying()
        ->withHeaders([
            'bs-session-id' => $this->sessionId, // Pass session ID for authentication
            'accept' => 'application/json',
        ])
        ->get($this->apiUrl . '/cards');
    //dd($getAllCards->json()['CardCollection']['total']);
    
        for($i=0; $i < $getAllCards->json()['CardCollection']['total']; $i++){
    
            if($getAllCards->json()['CardCollection']['rows'][$i]['card_id']==$this->cardnumber){
    
                if($getAllCards->json()['CardCollection']['rows'][$i]['is_assigned']=='false'){
                   
                    $assigncard = Http::withoutVerifying()
                    ->withHeaders([
                        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                        'accept' => 'application/json',
                    ])
                   
                    ->put($this->apiUrl . '/users/'.$this->userID,[
                        'User' => [
                            'cards' => [
                                ['id' => $getAllCards->json()['CardCollection']['rows'][$i]['id']]
                            ]
                        ]
                        
                    ]);
                   
                session()->flash('success', 'Card is successfully assigned.');
                }else{
                    session()->flash('error', 'Card is already assigned to a user.');
                }
    
                
            }else{
                session()->flash('error', 'Card is not found. Please first add card to the system.');
            }
        }
           
          }
      
    if ($response->successful()) {

       

     
        session()->flash('success', 'User created successfully.');

        return redirect()->route('items');
        
    } else {
        session()->flash( 'error', 'User created not successfully.');
    }
        
    }

    public function cancel(){
        
        return redirect()->route('items');
    }

    public function render()
    {
        return view('livewire.user-register-form');
    }
}
