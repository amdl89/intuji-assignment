<?php
require_once __DIR__.'/../../../bootstrap/app.php';

use App\Auth\AuthFactory;
use App\Database\DBFactory;
use App\Utils\Config;
use App\Utils\Redirect;
use Google\Service\Calendar;
use Google\Service\Oauth2;

if (!isset($_GET['code'])) {
    $_SESSION['FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => 'No authorization code received',
    ];
    Redirect::path('/home.php');
}

$googleConfig = Config::get('google');

$client = new Google_Client();
$client->setClientId($googleConfig['google_client_id']);
$client->setClientSecret($googleConfig['google_client_secret']);
$client->setRedirectUri($googleConfig['google_calendar_redirect_uri']);

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code'], $_SESSION['code_verifier']);
    unset($_SESSION['code_verifier']);

    if (isset($token['error'])) {
        throw new \Exception('Error fetching token for user: '.$token['error']);
    }

    $client->setAccessToken($token);

    // Decode the ID token to retrieve user information
    $idToken = $token['id_token'];
    $payload = $client->verifyIdToken($idToken);

    if(!$payload) {
        throw new \Exception('Invalid id token');
    }

    $accessToken = $client->getAccessToken();

    // verify requested scopes were approved by user
    $neededScopes = [
        Oauth2::OPENID,
        Calendar::CALENDAR_EVENTS,
    ];
    $scopesGrantedByUser = explode(' ',$accessToken['scope']);
    $allPresent = empty(array_diff($neededScopes, $scopesGrantedByUser));
    if(!$allPresent) {
        $client->revokeToken($client->getAccessToken());
        throw new \Exception('Insufficient permissions granted');
    }

    $refreshToken = $accessToken['refresh_token'];
    $sid = $payload['sub'];

    $db = DBFactory::getDB();
    $auth = AuthFactory::getAuth();

    // check if users sid is the logged-in user's sid
    $authUser = $auth->getUserInfo();
    if($authUser['sid'] !== $sid) {
        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'error',
            'message' => "Invalid user received. Please try again by selecting google account with which you logged in.",
        ];
        Redirect::path('/home.php');
    }

    // update tokens for user
    $updateUserTokensQuery = 'UPDATE USERS SET calendar_access_token = :calendar_access_token, calendar_refresh_token = :calendar_refresh_token WHERE sid = :sid';
    $updateUserTokensQueryParams = [
        ':calendar_access_token' => json_encode($accessToken),
        ':calendar_refresh_token' => $refreshToken,
        ':sid' => $sid,
    ];
    if($db->query($updateUserTokensQuery, $updateUserTokensQueryParams)) {
        // login user again to refresh userinfo in session
        if($auth->loginUsingId($authUser['id'])) {
            Redirect::path('/home.php');
        } else {
            $_SESSION['__FLASH_MESSAGE'] = [
                'type' => 'error',
                'message' => "Error refreshing user session. Please login again.",
            ];
            Redirect::path('/logout.php');
        }

    }
    throw new \Exception('DB failure');

} catch (\Exception $e) {
    $errMsg = $e->getMessage() ?? 'Something went wrong';
    $_SESSION['__FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => "{$errMsg}. Please try again.",
    ];
    Redirect::path('/home.php');
}
