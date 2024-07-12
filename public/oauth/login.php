<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Utils\Config;
use App\Utils\Redirect;
use Google\Service\Oauth2;

$googleConfig = Config::get('google');

$client = new Google_Client();
$client->setClientId($googleConfig['google_client_id']);
$client->setClientSecret($googleConfig['google_client_secret']);
$client->setRedirectUri($googleConfig['google_login_redirect_uri']);
$client->setScopes([
    Oauth2::OPENID,
    Oauth2::USERINFO_EMAIL,
    Oauth2::USERINFO_PROFILE,
]);
$client->setAccessType('online');

$authUrl = $client->createAuthUrl();

Redirect::path($authUrl);