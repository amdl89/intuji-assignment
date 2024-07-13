<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Auth\AuthFactory;
use App\Database\DBFactory;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;

if (!isset($_POST['eventId'])) {
    Redirect::path('/home.php');
}

function getEvent()
{
    $service = GoogleClientFactory::getCalendarServiceForAuthUser();
    $eventObj = $service->events->get('primary', $_POST['eventId']);

    $event = [
        'id' => $eventObj->getId(),
        'summary' => $eventObj->getSummary() ?? '',
        'description' => $eventObj->getDescription() ?? '',
        'timeMin' => date('Y-m-d\TH:i', strtotime($eventObj->getStart()->getDateTime())),
        'timeMax' => date('Y-m-d\TH:i', strtotime($eventObj->getEnd()->getDateTime())),
        'status' => $eventObj->getStatus(),
    ];
    $eventFormAction = '/events/update.php';

    require __DIR__.'/../../src/Views/calendar/editEvent.php';
}

$db = DBFactory::getDB();

$auth = AuthFactory::getAuth();
$authUserInfo = $auth->getUserInfo();

$client = GoogleClientFactory::getClient($authUserInfo['calendar_access_token']);

$PAGE_TITLE = 'Edit event';

try {
    getEvent();
} catch (\Google\Service\Exception $e) {
    if ($e->getCode() === 401) {
        // Handle access token expired error
        try {
            $token = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $client->setAccessToken($token);

            $accessToken = $client->getAccessToken();
            $refreshToken = $accessToken['refresh_token'];

            // update token for user
            $updateUserTokensQuery = 'UPDATE USERS SET calendar_access_token = :calendar_access_token, calendar_refresh_token = :calendar_refresh_token WHERE sid = :sid';
            $updateUserTokensQueryParams = [
                ':calendar_access_token' => json_encode($accessToken),
                ':calendar_refresh_token' => $refreshToken,
                ':sid' => $authUserInfo['sid'],
            ];
            $db->query($updateUserTokensQuery, $updateUserTokensQueryParams);

            // login user again to refresh userinfo in session
            $auth->loginUsingId($authUserInfo['id']);

        } catch (Exception $e) {
            // set refresh token to null for user since it is expired
            $setUserRefreshTokenToNullQuery = 'UPDATE USERS SET calendar_refresh_token = null WHERE id = :id';
            $db->query($setUserRefreshTokenToNullQuery, [':id' => $authUserInfo['id'],]);

            // login user again to refresh userinfo in session
            $auth->loginUsingId($authUserInfo['id']);

            $_SESSION['__FLASH_MESSAGE'] = [
                'type' => 'error',
                'message' => "Looks like you have been disconnected from your google account. Please connect again.",
            ];
            Redirect::path('/home.php');
            return;
        }
        try {
            getEvent();
        } catch (\Exception $e) {
            $_SESSION['__FLASH_MESSAGE'] = [
                'type' => 'error',
                'message' => 'Error fetching event.',
            ];
            Redirect::path('/home.php');
        }
    } else {
        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'error',
            'message' => 'Error fetching event.',
        ];
        Redirect::path('/home.php');
    }
} catch (\Exception $e) {
    $_SESSION['__FLASH_MESSAGE'] = [
        'type' => 'error',
        'message' => 'Error fetching event.',
    ];
    Redirect::path('/home.php');
}
