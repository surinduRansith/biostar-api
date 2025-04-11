<?php

namespace App\Livewire;

use App\Http\Controllers\BiostarLoginDetails;
use App\Models\bs2users;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class ItemCategorys extends Component
{
   
    use WithFileUploads;
public $apikey=[];

public $template;
public $scanfinger=[];
public $sessionId;
public $name="";
    public $user_id="";
    public $apiUrl;
    public $cardnumber=0;
    

    public $image;
  

    public function userdetails()
    {
      
        $biostarLoginDetails = new BiostarLoginDetails();
        
        $this->sessionId =$biostarLoginDetails->sessionId;
        $this->apiUrl=$biostarLoginDetails->apiUrl;
        
            // Step 2: Make the user search request with the session ID
            $searchResponse = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->post($this->apiUrl . '/v2/users/search', [
                    'limit' => 0,
                    'search_text' => null,
                    'user_group_id' => '1',
                    'order_by' => 'user_id:false',
                ]);
        
            $this->apikey=$searchResponse->json();
       //dd($this->apikey);
    }
    public function createUser() {

        $this->userdetails(); // Ensure session ID is set
      // dd($this->user_id);
        // Make API Request
        $response = Http::withoutVerifying()
            ->withHeaders([
                'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                'accept' => 'application/json',
            ])
            ->post($this->apiUrl . '/users', [
                'User' => [
                    'user_id' => $this->user_id, 
                    'user_group_id' => ['id' => 1], // ✅ FIXED: Changed from array to integer
                    'start_datetime' => '2001-01-01T00:00:00.00Z',
                    'expiry_datetime' => '2030-12-31T23:59:00.00Z',
                    'disabled' => false,
                    'name' =>   $this->name,
                    // 'email' => 'testuser@example.com',
                    // 'department' => '',
                    // 'user_title' => '',
                    // 'photo' => '',
                    // 'phone' => '010-1111-2222',
                    // 'permission' => 1,  // ✅ FIXED: Ensure it's a number
                    // 'access_groups' => [1], // ✅ FIXED: Use array of IDs
                    // 'login_id' => '',
                    // 'password' => '',
                    // 'user_ip' => '127.0.0.1',
                ],
            ]);

            $validate = $this->validate([
                
                'image' => 'nullable|image|sometimes|max:10240',
            ]);
            
            if ($this->image) {
                $validate['image']= $this->image->store('uploads', 'public');
                
            }
            //dd($validate['image']);

            bs2users::create([
                'userid' => $this->user_id,
                'name' => $this->name,
                'image' => $validate['image']
            ]);
    

        // Handle Response
        if ($response->successful()) {
            session()->flash('success', 'User created successfully.');

            $this->reset('user_id','name','image');
            
        } else {
            session()->flash( 'error', 'User created not successfully.');
        }

 
}

public function deleteUser($user_id) {

 
      // Make API Request
      $response = Http::withoutVerifying()
      ->withHeaders([
          'bs-session-id' => $this->sessionId, // Pass session ID for authentication
          'accept' => 'application/json',
      ])
      ->delete($this->apiUrl . '/users/'.$user_id);

     bs2users::where('userid', $user_id)->delete();
    
  // Handle Response
  if ($response->successful()) {
      session()->flash('success', "User ID $user_id deleted successfully!");
  } else {
      $error = $response->json();
      session()->flash('error', "Error deleting user: " . ($error['message'] ?? 'Unknown error'));
  }
}


public function scan(){

    $response = Http::withoutVerifying()
    ->withHeaders([
        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
        'accept' => 'application/json',
    ])
    ->post($this->apiUrl . '/devices/546833105/scan_fingerprint', [
        "ScanFingerprintOption" => [
            "enroll_quality" => "80",
            "raw_image" => true
        ]
    ]);
//dd($response->json());
    $this->template=$response->json()['FingerprintTemplate']['template0'];
   
      
        $this->scanfinger[]=[
            'temp1'=>$this->template
        ];

}
 
public function addfinger($user_id){

    $response = Http::withoutVerifying()
    ->withHeaders([
        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
        'accept' => 'application/json',
    ])
    ->put($this->apiUrl . '/users/'.$user_id,[
        "User" => [
            "fingerprint_templates" => [
                [
                    "finger_mask" => "false",
                    "template0" => $this->scanfinger[0]['temp1'],
                    "template1" =>$this->scanfinger[0]['temp1']
                ],
                [
                    "finger_mask" => "false",
                    "template0" => $this->scanfinger[1]['temp1'],
                    "template1" =>$this->scanfinger[1]['temp1']
                ],
               
             
            ]
        ]
    ]);
    $this->reset('scanfinger');
    session()->flash('success', 'Fingerprints are successfully added.');
    
    
}


// public function scanCard(){
    
//     $response = Http::withoutVerifying()
//     ->withHeaders([
//         'bs-session-id' => $this->sessionId, // Pass session ID for authentication
//         'accept' => 'application/json',
//     ])
//     ->post($this->apiUrl . '/devices/546833105/scan_card');

//     $this->cardnumber=$response->json()['Card']['card_id'];

//     $addcsncard = Http::withoutVerifying()
//     ->withHeaders([
//         'bs-session-id' => $this->sessionId, // Pass session ID for authentication
//         'accept' => 'application/json',
//     ])
//     ->post($this->apiUrl . '/cards',[
//         "CardCollection" => [
//             "rows" => [
//                 [
//                     "card_id" => $this->cardnumber,
//                     "card_type" => [
//                         "id" => $response->json()['Card']['card_type']['id'],
//                         "type" => $response->json()['Card']['card_type']['type']
//                     ]
//                 ]
//             ]
//         ]
//     ]);






// }

public function addCard($user_id){

    // $response = Http::withoutVerifying()
    // ->withHeaders([
    //     'bs-session-id' => $this->sessionId, // Pass session ID for authentication
    //     'accept' => 'application/json',
    // ])
    // ->post($this->apiUrl . '/devices/546833105/scan_card');

    // $this->cardnumber=$response->json()['Card']['card_id'];

    $response = Http::withoutVerifying()
    ->withHeaders([
        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
        'accept' => 'application/json',
    ])
    ->post($this->apiUrl . '/devices/546833105/scan_card');

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
               
                ->put($this->apiUrl . '/users/'.$user_id,[
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



  //dd($getAllCards->json(),$this->cardnumber);  
}
public function scanVisualFace($user_id){

    $addcsncard = Http::withoutVerifying()
    ->withHeaders([
        'bs-session-id' => $this->sessionId, // Pass session ID for authentication
        'accept' => 'application/json',
    ])
    ->put($this->apiUrl . '/users/' . $user_id, [
        'User' => [
            'credentials' => [
                'visualFaces' => [
                    [
                        'template_ex_normalized_image' => '/9j/4AAQSkZJRgABAQEASABIAAD/ Truncated',
                        'templates' => [
                            [
                                'credential_bin_type' => '5',
                                'template_ex' => 'AAABAAEAKgHyAaUA1wBj8vP2XEpVQSt0dQAAhQApwwBkcAAAOQAAAIR9eH6CiHmDeIJ+hHp/fX54eoOMdol5f4d9fYGHfoSAfHiAenuJgYZ8g3yAfXmBg36CgoGGhn6Ce3eDeXp3eIWGbnqGfJF4hIl4cX54gX+Ceod8cIJ9g32CgH+AhoeBgH12go57h4F4iXh6g31+gX+BdnyKhYKCfXqLeX2AgomMfYR/foKDgXt7hHp/fXh6g315dnp/f4N8f3h9g3Z+c3J7hH9/gYaFfoGKd3yHeXl/d3+AeXd+fYR8fn2Dd4OGfoF8hIGCgn+DfH+HhXp/e4V9fHiAhXeBgX94f3F6hIeBent8fYCAg4J1hnl+c4F6doZ9g32JhnuDg3l4fXyEg3uEfYmEh3yCf4Z2gH2BgoZ/g3p2hXuIhIOCfYKBfXd8hIeDdYSFfHSDg3R+d4eBeY98gXp1dH59fHyBhXl4hG+Aen2De4OCgIGKhXmFgnuHfH98hIl6fIZ0gIB2homJgHuFf4OKhYZ8hHqFgoB9jYeGgX57iYGCfIV6f4aBfnqAhoV9e4KBgYmDgH55g4Z8hoCDhIR8gIOHfniChIB5hXl/gIV4f4l+g3V5hn5/f45/fHmAgIB5gIR8fYiGeoKBdXZ+eIB+gneDenl+fIGBgIeEf4CCjIJ3eYKCiX13gHh9eIeChX6FdYGFfYSFhn6EhYCGf4h5e4OLfYl9gIN3fYR8'
                            ],
                            [
                                'credential_bin_type' => '9',
                                'template_ex' => 'AAABAAEAVgHZAa8A4wAAAAAAWFVLREUAAAAAdVANMQFkcAAAOQAAAIWEdn+Din+Ed4GCg3eAhHl4gX6OdI56foZ7e3yGe4d7d4OIfnaFgYJ/goB7eniCgXx8gYSEhHqBfXp/eoJ2f4GFcYCAfYV7gYZ3d3l4hHqEfI94cYCDg3+Bf397iYyAgHx7gIp4hol2gnx7hYGAe4KGf4J+gYR+fneJd319gouMf4CAgoWKe3p/hH1/g3pygH57eYGCfop/eoCAi3l6c3d7hIaAiICCfnyIc3qCdXx9en2AeH6DgXp9e4CDe4yGfIKEfoKGgXiCgYOEhnl4foCDf3uFg3qBgIF2g3J8foSBeXh7gYOCgYB6iIR9enx7dYR/fX6AgoCCgXh8fXyAeXuAfYmGiXd9f4p2gXqBgoh7hXl8hXuCgoGCgYZ+e3p/hYSBgomLgnOBf3uCe4WEhIx6fnt4eYZ8gnt7hnp7gHqFfoF/fYCAhIKCgnaDenyJf3t+goh4hYV2goN7g4eNfYOBd4KLgoSBh3qAhn5/joGDgYF6gH6HfYlzhop9hH6AeYGAgH9/gIt/iYJ+god9jYOCgoR/g4WCg3WCg4B4hXl8fYR5iYGAg353g3R6fYd7d3WHgX1xf3t4gYWIeYKEe3h8eoOBf3WGdYCChIR7foiDgX56hYh+eX6AiXuBfXiAdomAeoB+doSCf4aFgXmDgX2AfYp9fH2KfIR6foJwfIOB'
                            ]
                        ]
                    ],
                    [
                        'template_ex_normalized_image' => '/9j/4AAQSkZJRgABAQEASABIAAD/ Truncated',
                        'templates' => [
                            [
                                'credential_bin_type' => '5',
                                'template_ex' => 'AAABAAEAgwFoAbgA6gBl8vryXUdWNip7hwAAnKA+uQBkcAAAOQAAAIZ7eX5/iX2CeH59iXuAgH1vfoWNdop8gIqAfoCIe4iAdYGFenmHgId7fH5+fHN/g3+GgICCgHmCe3mEc3tze4uGdH+IgYt5hIR4dYN4gn+AeoV8b4B8g3qAh4GFiId/f391gYp3foJ2iHp6gXuAg3uBgH+Jg4F8fneHgICEg4aMfH9/fISEgH17iHh4fnp1hIF/fXyBeYaAfn96hXZ8e3R+gX98hYOAf32GeX+Ie3x9d314fHyBgIR6fYKDeIWHf4KDiICFhnuAeoB7gXeEe4B8fnl/h3d5hIJ6fXR5f4aFfXyBe4B+hIRziYN6eYR7eH9/gYCEgHuCgHp4fYOHgoN/eomGh3Z7e4Vzf3qAjYt+gH16gX2Dgn6DfIOAfnZ6hYuAfISKgXiFgHV9d4uBf4iFgHZ5d3x9gXx5gn55hHSCfn6AfYSBhIKIhnSFhYGMen58gYd7gIN2fnl5h4SLgnmHfX2HgYV7gH2Fe4F7h4GBfn98iH+DfYl5gIJ9g319gIV/g4J8gYV/fHt5hIN9i4KEg35+h4eJg4B8fHp7gXl+hYJzf4V6gntxhn9+eoR/fHuDfHx7gIJ4f4SBfH9+doB9d4F/fXaFdHl9eoOEfoWEf3uEhoV0doCCjHt0hXl+coOAg4ODdYSGgIOHh3yBioCEeYx7eXyMf4l/hYF1foWD'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);

dd($addcsncard->json());
} 
    public function render()
    {
        $this->userdetails();
        return view('livewire.item-categorys');
    }
}
