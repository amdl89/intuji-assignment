<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Auth\Guards;
use App\Services\GoogleClientFactory;
use App\Utils\Config;
use App\Utils\Redirect;
use Google\Service\Calendar;
use Google\Service\Oauth2;

Guards::redirectIfNotAuthenticated();

$codeVerifier = bin2hex(random_bytes(32)); // Generate a random code verifier
$codeChallenge =  rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
$_SESSION['code_verifier'] = $codeVerifier;

$googleConfig = Config::get('google');

$client = GoogleClientFactory::getClient();
$client->setRedirectUri($googleConfig['google_calendar_redirect_uri']);
$client->setScopes([
    Oauth2::OPENID,
    Calendar::CALENDAR_EVENTS,
]);
$client->setPrompt('consent');
$client->setAccessType('offline');

$authUrl = $client->createAuthUrl(queryParams: [
    'code_challenge' => $codeChallenge,
    'code_challenge_method' => 'S256',
]);

Redirect::path($authUrl);