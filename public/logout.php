<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Utils\Config;
use App\Utils\Redirect;

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();

    // revoke access token
    $access_token = $_SESSION['__ACCESS_TOKEN'] ?? null;

    if ($access_token) {
        $googleConfig = Config::get('google');

        $client = new Google_Client();
        $client->setClientId($googleConfig['google_client_id']);
        $client->setClientSecret($googleConfig['google_client_secret']);
        $client->setAccessToken($access_token);

        // Revoke the token
        $client->revokeToken($client->getAccessToken());
    }
}

Redirect::path('/welcome.php');