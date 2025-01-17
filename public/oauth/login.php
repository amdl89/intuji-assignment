<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Auth\Guards;
use App\Services\GoogleClientFactory;
use App\Utils\Config;
use App\Utils\Redirect;
use Google\Service\Oauth2;

Guards::redirectIfAuthenticated();

$codeVerifier = bin2hex(random_bytes(32)); // Generate a random code verifier
$codeChallenge =  rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
$_SESSION['code_verifier'] = $codeVerifier;

$googleConfig = Config::get('google');

$client = GoogleClientFactory::getClient();
$client->setRedirectUri($googleConfig['google_login_redirect_uri']);
$client->setScopes([
    Oauth2::OPENID,
    Oauth2::USERINFO_EMAIL,
    Oauth2::USERINFO_PROFILE,
]);
$client->setPrompt('select_account');
$client->setApprovalPrompt('force');
$client->setAccessType('online');

$authUrl = $client->createAuthUrl(queryParams: [
    'code_challenge' => $codeChallenge,
    'code_challenge_method' => 'S256',
]);

Redirect::path($authUrl);