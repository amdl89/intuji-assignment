<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Services\GoogleClientFactory;
use App\Utils\Redirect;

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();

    // revoke access token
    $access_token = $_SESSION['__ACCESS_TOKEN'] ?? null;

    if ($access_token) {
        $client = GoogleClientFactory::getClient($access_token);
        // Revoke the token
        $client->revokeToken($client->getAccessToken());
    }
}

Redirect::path('/welcome.php');