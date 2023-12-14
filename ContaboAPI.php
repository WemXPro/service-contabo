<?php

namespace App\Services\Contabo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ContaboAPI
{
    /**
     * Init connection with API
    */
    public function api($method, $endpoint, $data = [])
    {
        $authenticate = Http::asForm()->post('https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token', [
            'client_id' => settings('contabo::client_id'),
            'client_secret' => settings('encrypted::contabo::client_secret'),
            'username' => settings('contabo::username'),
            'password' => settings('encrypted::contabo::user_password'),
            'grant_type' => 'password',
        ]);

        if($authenticate->failed())
        {
            if($authenticate->unauthorized() OR $authenticate->forbidden()) {
                throw new \Exception("[Contabo] This action is unauthorized! Confirm that the config is setup correctly");
            }

            // dd($authenticate);
            if($authenticate->serverError()) {
                throw new \Exception("[Contabo] Internal Server Error: {$authenticate->status()}");
            }

            throw new \Exception("[Contabo] Failed to connect to the API! Confirm that the config is setup correctly");
        }

        if(!isset($authenticate['access_token'])) {
            throw new \Exception("[Contabo] Access token was not returned from the Contabo API.");
        }

        $accessToken = $authenticate['access_token'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'x-request-id' => (string) Str::uuid(),    
        ])->$method("https://api.contabo.com/v1{$endpoint}", $data);
        
        if($response->failed())
        {
            if($response->unauthorized() OR $response->forbidden()) {
                throw new \Exception("[Contabo] This action is unauthorized! Confirm that the config is setup correctly");
            }

            // dd($response);
            if($response->serverError()) {
                throw new \Exception("[Contabo] Internal Server Error: {$response->status()}");
            }

            throw new \Exception("[Contabo] Failed to connect to the API! Confirm that the config is setup correctly");
        }

        return $response;
    }

    /**
     * Get the available images as a Laravel collection
    */
    public function getImages()
    {
        return $this->api('get', '/compute/images', [
            'size' => 100,
        ])->collect();
    }

    /**
     * List all available servers
    */
    public function getServers()
    {
        return $this->api('get', '/compute/instances', [
            'size' => 100,
        ])->collect();
    }

    /**
     * Create a new server
    */
    public function createServer($data)
    {
        return $this->api('post', "/compute/instances", [
            'displayName' => $data['display_name'],
            'imageId' => $data['image'],
            'productId' => $data['product'],
            'region' => $data['region'],
            'period' => $data['period'],
        ])->collect();
    }

    /**
     * Get a server from ID
    */
    public function getServer($serverID)
    {
        return $this->api('get', "/compute/instances/{$serverID}")->collect();
    }

    /**
     * Get a server servers logs from ID
    */
    public function getServerLogs($serverID)
    {
        return $this->api('get', "/compute/instances/actions/audits", [
            'instanceId' => $serverID,
        ])->collect();
    }

    /**
     * cancel a server from ID
    */
    public function cancelServer($serverID): void
    {
        try {
            $this->api('post', "/compute/instances/{$serverID}/cancel")->collect();
        } catch (\Exception $e) {
            ErrorLog("contabo::cancel::server::{$serverID}", $e->getMessage(), 'CRITICAL');
        }
    }
    
    /**
     * start a server from ID
    */
    public function startServer($serverID)
    {
        return $this->api('post', "/compute/instances/{$serverID}/actions/start")->collect();
    }

    /**
     * stop a server from ID
    */
    public function stopServer($serverID)
    {
        return $this->api('post', "/compute/instances/{$serverID}/actions/stop")->collect();
    }

    /**
     * shutdown a server from ID
    */
    public function shutdownServer($serverID)
    {
        return $this->api('post', "/compute/instances/{$serverID}/actions/shutdown")->collect();
    }

    /**
     * enable rescue mode for a server from ID
    */
    public function enableRescueMode($serverID)
    {
        return $this->api('post', "/compute/instances/{$serverID}/actions/rescue")->collect();
    }

    /**
     * reset password for a server from ID
    */
    public function resetPassword($serverID, $password)
    {
        return $this->api('post', "/compute/instances/{$serverID}/actions/resetPassword", [
            'rootPassword' => $password,
        ])->collect();
    }
}