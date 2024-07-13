<?php

namespace App\Services;

use App\Utils\Config;

class GoogleClientFactory
{
    public static function getClient($token = null): \Google_Client
    {
        $googleConfig = Config::get('google');

        $client = new \Google_Client();
        $client->setClientId($googleConfig['google_client_id']);
        $client->setClientSecret($googleConfig['google_client_secret']);
        if ($token) {
            $client->setAccessToken($token);
        }
        return $client;
    }

}