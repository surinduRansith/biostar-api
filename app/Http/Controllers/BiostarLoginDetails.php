<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BiostarLoginDetails extends Controller
{
   
    public $apiUrl;
    private $apiuserName;
    
    private $apipassword;

    public $sessionId;

    public function __construct()
    {
        $this->apiUrl = env('BIOSTAR_BASE_URL');
        $this->apiuserName = env('BIOSTAR_USERNAME');
        $this->apipassword = env('BIOSTAR_PASSWORD');

        $loginResponse = Http::withoutVerifying()
        ->withHeaders([
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
        ->post($this->apiUrl . '/login', [
            'User' => [
                'login_id' => $this->apiuserName,
                'password' => $this->apipassword,
            ],
        ]);

    // Extract the session ID from the headers
    $this->sessionId = $loginResponse->header('bs-session-id');

    if (!$this->sessionId) {
       echo "<div class='alert bg-red-600 text-white text-sm p-5 rounded-lg shadow-xl flex items-center justify-center mb-6 space-x-4'>
    <div>
        <span class='font-semibold text-lg'>Not Connected to the API!</span>
        <p class='text-sm mt-1'>Please check your connection and ensure the API is reachable. If the issue persists, contact support.</p>
    </div>
</div>";
    }

    }

}
