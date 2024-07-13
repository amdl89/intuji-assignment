<?php
require_once __DIR__.'/../../../bootstrap/app.php';

use App\Auth\AuthFactory;
use App\Database\DBFactory;
use App\Utils\Config;
use App\Utils\Redirect;
use Google\Service\Oauth2;

if (!isset($_GET['code'])) {
    $_SESSION['FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => 'No authorization code received',
    ];
    Redirect::path('/welcome.php');
}

$googleConfig = Config::get('google');

$client = new Google_Client();
$client->setClientId($googleConfig['google_client_id']);
$client->setClientSecret($googleConfig['google_client_secret']);
$client->setRedirectUri($googleConfig['google_login_redirect_uri']);

try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code'], $_SESSION['code_verifier']);
    unset($_SESSION['code_verifier']);

    if (isset($accessToken['error'])) {
        throw new \Exception('Error fetching token for user: '.$accessToken['error']);
    }

    $oauth2 = new Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $db = DBFactory::getDB();
    $auth = AuthFactory::getAuth();

    $userQuery = 'SELECT id, sid FROM users where sid = :sid LIMIT 1';
    if($user = $db->fetchOne($userQuery, [':sid' => $userInfo->getId()])) {
        // user already exists, login user and redirect to home
        if($auth->loginUsingId($user['id'])) {
            Redirect::path('/home.php');
        }
        throw new \Exception('Login failed');
    }

    // register user, login user and redirect to home
    $insertUserQuery = 'INSERT INTO USERS (sid, email, name, picture) VALUES (:sid, :email, :name, :picture)';
    $insertUserQueryParams = [
        ':sid' => $userInfo->getId(),
        ':email' => $userInfo->getEmail(),
        ':name' => $userInfo->getName(),
        ':picture' => $userInfo->getPicture(),
    ];
    if($db->query($insertUserQuery, $insertUserQueryParams)) {
        if($auth->loginUsingId($db->getLastInsertId())) {
            Redirect::path('/home.php');
        }
        throw new \Exception('Login failed');
    }
    throw new \Exception('Registration failed');

} catch (\Exception $e) {
    $errMsg = $e->getMessage() ?? 'Something went wrong';
    $_SESSION['__FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => "{$errMsg}. Please try again.",
    ];
    Redirect::path('/welcome.php');
}
