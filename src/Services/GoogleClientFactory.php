<?php

namespace App\Services;

use App\Auth\AuthFactory;
use App\Utils\Config;
use Google\Service\Calendar;

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

    public static function getCalendarServiceForAuthUser(): Calendar
    {
        $auth = AuthFactory::getAuth();
        $authUserInfo = $auth->getUserInfo();

        if(!$authUserInfo) {
            throw new \Exception('User not logged in.');
        }
        $googleClient = static::getClient($authUserInfo['calendar_access_token']);
        return new Calendar($googleClient);
    }

}