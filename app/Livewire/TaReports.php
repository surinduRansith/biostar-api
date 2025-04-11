<?php

namespace App\Livewire;

use Livewire\Component;
use App\Http\Controllers\BiostarLoginDetails;
use App\Models\shiftByUsers;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;

class TaReports extends Component
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

    public $image;
    public $setUserID;
    public $result= '';


    public $firstinandlast = [];


    public function updateduserSearch($value)
    {


        $biostarLoginDetails = new BiostarLoginDetails();

        $this->sessionId = $biostarLoginDetails->sessionId;
        $this->apiUrl = $biostarLoginDetails->apiUrl;

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


    public function resetserach()
    {
        $this->userSearch = '';
        $this->userlists = [];

    }

    public function setUserid($userid)
    {


        $this->setUserID = $userid;

        $this->reset('userSearch');
    }


    public function updatedStartDate($value)
    {
        $this->StartDate = $value;
    }

    public function updatedEndDate($value)
    {
        $this->EndDate = $value;
    }

    public function eventData()
    {
        $this->reset('eventlists', 'events', 'eventNames','firstinandlast');
        if ($this->StartDate != null || $this->EndDate != null || $this->setUserID != null) {


            $eventcodes = [
                '4097',
                '4098',
                '4099',
                '4100',
                '4101',
                '4102',
                '4103',
                '4104',
                '4105',
                '4106',
                '4107',
                '4610',
                '4611',
                '4616',
                '4617',
                '4865',
                '4866',
                '4867',
                '5377',
                '5378'
            ];

            $biostarLoginDetails = new BiostarLoginDetails();

            $this->sessionId = $biostarLoginDetails->sessionId;
            $this->apiUrl = $biostarLoginDetails->apiUrl;


            //dd($this->StartDate ."Z", $this->EndDate);





            // Step 2: Make the user search request with the session ID
            $searchResponse = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->post($this->apiUrl . '/events/search', [
                    "Query" => [
                        "limit" => 1000,
                        "conditions" => [
                            [
                                "column" => "datetime",
                                "operator" => 3,
                                "values" => [
                                    (new DateTime($this->StartDate))->format('Y-m-d\TH:i:s\Z'),
                                    (new DateTime($this->EndDate))->format('Y-m-d\TH:i:s\Z')
                                ]
                            ],
                            [
                                "column" => "user_id.user_id",
                                "operator" => 0,
                                "values" => [
                                    $this->setUserID
                                ]
                            ]


                        ],
                        "orders" => [
                            [
                                "column" => "datetime",
                                "descending" => false
                            ]

                        ]
                    ]
                ]);

            $this->events = $searchResponse->json()['EventCollection']['rows'];


            $geteventnames = Http::withoutVerifying()
                ->withHeaders([
                    'bs-session-id' => $this->sessionId, // Pass session ID for authentication
                    'accept' => 'application/json',
                ])
                ->get($this->apiUrl . '/event_types?is_break_glass=false&setting_alert=false&setting_all=true');


            for ($i = 0; $i < $geteventnames->json()['EventTypeCollection']['total']; $i++) {


                //echo ($geteventnames->json()['EventTypeCollection']['rows'][$i]['code'].'<br>');

                $this->eventNames[] = [
                    'code' => $geteventnames->json()['EventTypeCollection']['rows'][$i]['code'],
                    'name' => $geteventnames->json()['EventTypeCollection']['rows'][$i]['name']
                ];


            }

            //dd($this->eventNames);

            //dd(count($this->events));


            if ($this->events == "") {

                session()->flash('message', 'No data found. Please select the correct date range');
            } else {

                foreach ($this->events as $index => $event) {
                    //dd(count($this->events));

                    if (in_array($event['event_type_id']['code'], $eventcodes)) {

                        foreach ($this->eventNames as $eventname) {

                            if ($event['event_type_id']['code'] == $eventname['code']) {
                                //print_r($eventname['code']);
                                $date = new DateTime($this->events[$index]['datetime']);
                                $date->setTimezone(new DateTimeZone('Asia/Kolkata')); // IST (UTC+5:30)

                                //dd(count($this->eventlists));

                                $this->eventlists[] = [
                                    'datetime' => $date->format('Y-m-d H:i:s'),
                                    'userid' => $this->events[$index]['user_id']['user_id'],
                                    'username' => $this->events[$index]['user_id']['name'],
                                    'eventname' => $eventname['name']
                                ];
                            }
                        }


                    }
                }
            }

            $groupedEvents = collect($this->eventlists)
                ->map(function ($event) {
                    // Extract only the date part (ignoring the time)
                    $event['date'] = \Carbon\Carbon::parse($event['datetime'])->format('Y-m-d');
                    return $event;
                })
                ->groupBy('date')
                ->map(function ($events) {
                    return $events->values(); // Re-index the events array
                });

            
            //dd($this->eventlists); 

            // Iterate through the grouped events
            foreach ($groupedEvents as $date => $events) {
               foreach ($events as $event) {
                   // dd($event['userid']);
               }
                // Extract times from the datetime and find the min and max times
                $times = collect($events)->map(function ($event) {
                    return \Carbon\Carbon::parse($event['datetime'])->format('H:i:s');
                });

                
                $minTime = $times->min(); // Get the earliest time
                $maxTime = $times->max(); // Get the latest time
                $useridinevent = $event['userid'];

                $addusersinshifts = ShiftByUsers::join('shifts', 'shift_by_users.shift_id', '=', 'shifts.id')
                ->where('shift_by_users.user_id', $useridinevent)
                ->select('shift_by_users.*', 'shifts.*') // Adjust column names
                ->get();
                if(!empty($addusersinshifts)){
                    
                    foreach($addusersinshifts as $addusersinshift){
                        //dd($minTime<=$addusersinshift['start_time'] && $maxTime>=$addusersinshift['end_time']);

                        if($minTime<=$addusersinshift['start_time'] && $maxTime>=$addusersinshift['end_time']){
                            $this->result= '-';
                        }elseif($minTime>$addusersinshift['start_time'] && $maxTime>=$addusersinshift['end_time']){
                            $this->result = 'Late IN';
                        }elseif($minTime<=$addusersinshift['start_time'] && $maxTime<$addusersinshift['end_time']){

                            $this->result = 'Early OUT';
                        }elseif($minTime>$addusersinshift['start_time'] && $maxTime<$addusersinshift['end_time']){

                            $this->result = 'Late IN, '.'Early OUT';

                        }elseif(empty($minTime)&&empty($maxTime)){
                            $this->result = 'Absene';
                        }else{
                            $this->result = 'no result';
                        }

                      


                    }
                }

               // dd($result);

                $this->firstinandlast[] = [
                    'date' => $date,
                    'minTime' => $minTime,
                    'maxTime' => $maxTime,
                    'userid' => $useridinevent,
                    'eventresult'=> $this->result
                ];
            //xprint_r($date. $minTime. $maxTime.$event['userid']);
                // dd($date, $minTime, $maxTime,$event['userid']);
                // echo "Date: $date\n";
                // echo "Minimum Time: $minTime\n";
                // echo "Maximum Time: $maxTime\n";




            }

           // dd($groupedEvents);
            // dd(count($this->eventlists));

            //$this->reset('setUserID');



        } else {
            session()->flash('message', 'Please select the date range and user');
        }


        // $dateString = $this->StartDate;
        // $dateTime = new DateTime($dateString);
        // $startTime = $dateTime->format('H:i:s');

        // $dateString = $this->EndDate;
        // $dateTime = new DateTime($dateString);
        // $endTime = $dateTime->format('H:i:s');

        $tashedule = ShiftByUsers::join('shifts', 'shift_by_users.shift_id', '=', 'shifts.id')
            ->where('shift_by_users.user_id', $this->setUserID)
            ->select('shift_by_users.*', 'shifts.*') // Adjust column names as needed
            ->get();

        if ($tashedule->isEmpty()) {
            session()->flash('message', 'No data found. Please select the correct date range');
        } else {

            foreach ($tashedule as $index => $event) {
               // dd($event);

            }
        }





    }



    public function render()
    {
        return view('livewire.ta-reports');
    }
}
