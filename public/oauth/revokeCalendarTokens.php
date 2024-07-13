<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Auth\AuthFactory;
use App\Database\DBFactory;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;

$auth = AuthFactory::getAuth();
$authUserInfo = $auth->getUserInfo();

$db = DBFactory::getDB();

$client = GoogleClientFactory::getClient($authUserInfo['calendar_access_token']);

// Revoke the token
$client->revokeToken($client->getAccessToken());

// delete tokens for user
$removeUserTokensQuery = 'UPDATE USERS SET calendar_refresh_token = null WHERE id = :id';
$db->query($removeUserTokensQuery, [':id' => $authUserInfo['id']]);

// login user again to refresh userinfo in session
$auth->loginUsingId($authUserInfo['id']);

$_SESSION['__FLASH_MESSAGE'] = [
    'type' => 'success',
    'message' => "Disconnected google calendar successfully.",
];
Redirect::path('/home.php');
