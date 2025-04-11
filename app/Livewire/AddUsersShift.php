<?php

namespace App\Livewire;

use App\Http\Controllers\BiostarLoginDetails;
use App\Models\shift;
use App\Models\shiftByUsers;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AddUsersShift extends Component
{
   
    public $sessionId;
    public $StartDate;
    public $EndDate;

    public $userId;

    public $events = [];
    public $eventNames = [];

    public $apiUrl;

    public $eventlists = [];

    public $userlists;

    public $userSearch;

    public $shiftId;

    public $image;
    public $setUserIDs=[];
    public $addusersinshifts;

    public function mount($shiftId)
    {
        $this->addusersinshifts = ShiftByUsers::join('shifts', 'shift_by_users.shift_id', '=', 'shifts.id')
        ->where('shift_by_users.shift_id', $shiftId)
        ->select('shift_by_users.*', 'shifts.shiftname') // Adjust column names
        ->get();
  
    }

    public function updateduserSearch($value)
    {
       
    
        $biostarLoginDetails = new BiostarLoginDetails();
     
        $this->sessionId =$biostarLoginDetails->sessionId;
        $this->apiUrl=$biostarLoginDetails->apiUrl;
        
        $searchResponse = Http::withoutVerifying()
        ->withHeaders([
            'bs-session-id' => $this->sessionId, // Pass session ID for authentication
            'accept' => 'application/json',
        ])
        ->post($this->apiUrl . '/v2/users/search', [
            'limit' => 200,
            'search_text' => $this->userSearch,
            'user_group_id' => '1',
           
        ]);
       
            $this->userlists = $searchResponse->json()['UserCollection']['rows'];  
           //dd($this->userlists); 
    

        
    }

    public function setUserid($userid, $name)
 { 


        $this->setUserIDs[] = [
            'userid'=>$userid,
            'name'=>$name
        ];

        $this->reset('userSearch');
}

public function removeUserid($index)
{
    unset($this->setUserIDs[$index]);
}

public function AddusersShfit(){
    if(empty($this->setUserIDs)){
       session()->flash('message', 'Please select atleast one user');
    }else{

    
    foreach($this->setUserIDs as $user){

        if(shiftByUsers::where('shift_id', $this->shiftId)->where('user_id', $user['userid'])->exists()){
            session()->flash('error', $user['userid'].' User already added');
        $this->reset('setUserIDs');

        }else{
        //dd($user['userid']);
        shiftByUsers::create([
            
            'shift_id' => $this->shiftId,
            'user_id' => $user['userid'],
            'name' => $user['name'],
            
        ]);

        session()->flash('message', 'User added successfully');
        $this->mount($this->shiftId);
        $this->reset('setUserIDs');
    }
    }
}
    }

    public function removeusershift($userid, $shiftid)
    {
        
       
        shiftByUsers::where('shift_id', $shiftid)->where('user_id', $userid)->delete();
        session()->flash('message', 'User removed successfully');
        $this->mount($shiftid);

    }

   
    public function render()
    {
        return view('livewire.add-users-shift');
    }
}
