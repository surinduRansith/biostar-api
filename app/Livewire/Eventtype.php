<?php

namespace App\Livewire;

use App\Http\Controllers\BiostarLoginDetails;
use App\Models\bs2users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Eventtype extends Component
{   public $sessionId;
    public $start_date;
    public $end_date;
    public $events = [];
    public $eventNames = [];

    public $apiUrl;
  
    
    public $image;

   



    public function mount()
    {
      
        $this->start_date = now()->subDays(1)->format('Y-m-d'); // Default: 7 days ago
        $this->end_date = now()->format('Y-m-d'); // Default: Today

        $this->eventData();
        $this->getEventName();
    }

    public function eventData()
    {

        $eventcodes = ['4097' ,'4098', '4099', '4100', '4101', '4102', '4103', '4104', '4105',
                        '4106', '4107', '4610', '4611', '4616', '4617', '4865', '4866', '4867',
                     '5377', '5378'];

        $biostarLoginDetails = new BiostarLoginDetails();
        
        $this->sessionId =$biostarLoginDetails->sessionId;
        $this->apiUrl=$biostarLoginDetails->apiUrl;
       
   // Validate date input
//    if (!$this->start_date || !$this->end_date) {
//     session()->flash('error', 'Please select both start and end dates.');
//     return;
// }
          // Convert to UTC+5:30 (Asia/Kolkata) in ISO 8601 format
//           $formattedStartDate = Carbon::parse($this->start_date . ' 00:00:00')
//           ->setTimezone('Asia/Kolkata')
//           ->format('Y-m-d\TH:i:s.000\Z');
          
// $formattedEndDate = Carbon::parse($this->end_date . ' 23:59:59')
//         ->setTimezone('Asia/Kolkata')
//         ->format('Y-m-d\TH:i:s.000\Z');
        
            // Step 1: Login to get session ID
           
        
           
            // Step 2: Make the user search request with the session ID
            $searchResponse = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->post($this->apiUrl . '/events/search',[
                    "Query" => [
                        "limit" => 1,
                        "conditions" => [
                        //     [
                        //         "column" => "datetime",
                        //         "operator" => 3,
                        //         "values" => [
                        //             $formattedStartDate,
                        //             $formattedEndDate
                        //         ]
                        //      ],
                        ],
                        "orders" => [
                            [
                                "column" => "datetime",
                                
                                "descending" => true
                            ]
                         ]
                    ]
                ]);
                
                $this->events=$searchResponse->json();
               
             
               if(!empty($this->events['EventCollection']['rows'][0]['user_id'])){
               
               

                    $find = bs2users::where('userid', $this->events['EventCollection']['rows'][0]['user_id'])->first();
                    if ($find == null){

                        $this->image=null;
                    }
                    else{

                        if(in_array($this->events['EventCollection']['rows'][0]['event_type_id']['code'], $eventcodes  )){

                            $this->image = $find->image;
                        }
                        
                    }
                
                }

                
           
       

    }

    public function getEventName(){
        $geteventnames = Http::withoutVerifying()
        ->withHeaders([
            'bs-session-id' => $this->sessionId, // Pass session ID for authentication
            'accept' => 'application/json',
        ])
        ->get($this->apiUrl . '/event_types?is_break_glass=false&setting_alert=false&setting_all=true');


    
        if(!empty($geteventnames->json()['EventTypeCollection'])){

            for($i=0; $i<$geteventnames->json()['EventTypeCollection']['total']; $i++){

           
                //echo ($geteventnames->json()['EventTypeCollection']['rows'][$i]['code'].'<br>');
    
                $this->eventNames[]=[
                    'code'=>$geteventnames->json()['EventTypeCollection']['rows'][$i]['code'],
                    'name'=>$geteventnames->json()['EventTypeCollection']['rows'][$i]['name']
                ];
    
     
            }
        }

       


    }

    public function render()
    {
       
        return view('livewire.eventtype');
    }
}
