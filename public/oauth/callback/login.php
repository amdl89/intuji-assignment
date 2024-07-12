<?php
require_once __DIR__.'/../../../bootstrap/app.php';

use App\Utils\Redirect;
use Google\Service\Oauth2;

if (!isset($_GET['code'])) {
    $_SESSION['FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => 'No authorization code received',
    ];
    Redirect::path('/welcome.php');
}

$client = new Google_Client();
$client->setClientId($config['google_client_id']);
$client->setClientSecret($config['google_client_secret']);
$client->setRedirectUri($config['google_redirect_uri']);

try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Check for errors in the access token response
    if (isset($accessToken['error'])) {
        throw new \Exception('Error fetching access token: '.$accessToken['error']);
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        session_start();
    }

    $oauth2 = new Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $_SESSION['user_info'] = $userInfo;
    $_SESSION['access_token'] = $accessToken;

} catch (\Exception $e) {
    $_SESSION['FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => 'Authorization error occurred. Please try again.',
    ];
    Redirect::path('/welcome.php');
}
