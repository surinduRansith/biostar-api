<?php

namespace App\Services;

use GuzzleHttp\Client;

class BiostarService
{
    protected $client;

    public function __construct()
    {
        
        $this->client = new Client([
            'base_uri' => env('BIOSTAR_BASE_URL'),
            'verify' => false, // Disable SSL verification for self-signed certs
        ]);
    }

    public function login()
    {
        try {
            $response = $this->client->post('login', [
                'json' => [
                    'login_id' => env('BIOSTAR_USERNAME'),
                    'password' => env('BIOSTAR_PASSWORD'),
                ],
            ]);
dd($response->getBody());
            $data = json_decode($response->getBody(), true);

            // Save the access token for future requests
            session(['biostar_token' => $data['token']]);

            return $data;
        } catch (\Exception $e) {
            // Handle errors
            return ['error' => $e->getMessage()];
        }
    }

    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . session('biostar_token'),
                ],
                'query' => $params,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
