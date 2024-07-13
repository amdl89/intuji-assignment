<?php

use App\Auth\AuthFactory;
use App\Database\DBFactory;
use App\Services\GoogleClientFactory;
use App\Utils\Config;
use App\Utils\Redirect;
use Google\Service\Calendar;

require_once __DIR__.'/../bootstrap/app.php';

function getEventsList() {

    $auth = AuthFactory::getAuth();
    $authUserInfo = $auth->getUserInfo();

    $client = GoogleClientFactory::getClient($authUserInfo['calendar_access_token']);
    $service = new Calendar($client);

    $optParams = [
        'orderBy' => 'startTime',
        'singleEvents' => true,
        ...array_filter([
            'timeMin' => isset($_GET['timeMin']) ? date('c', strtotime($_GET['timeMin'])) : null,
            'timeMax' => isset($_GET['timeMax']) ? date('c', strtotime($_GET['timeMax'])) : null,
            'maxResults' => isset($_GET['maxResults']) ? (int) $_GET['maxResults'] : 100,
            'showDeleted' => $_GET['showDeleted'] ?? null,
        ]),
    ];

    $eventsListRes = $service->events->listEvents('primary', $optParams);
    $eventsList = $eventsListRes['items'];

    require __DIR__.'/../src/Views/calendar/eventsList.php';
}

$db = DBFactory::getDB();

$auth = AuthFactory::getAuth();
$authUserInfo = $auth->getUserInfo();

$PAGE_TITLE = 'Home';

if (!$authUserInfo['calendar_access_token']) {
    // New user
    $calendarNotConnected_newUser = true;
    require __DIR__.'/../src/Views/calendar/userNotConnected.php';
} elseif (!$authUserInfo['calendar_refresh_token']) {
    // Refresh token has been revoked
    $calendarNotConnected_newUser = false;
    require __DIR__.'/../src/Views/calendar/userNotConnected.php';
} else {
    // show events view

    $client = GoogleClientFactory::getClient($authUserInfo['calendar_access_token']);

    try {
        getEventsList();
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
                getEventsList();
            } catch (\Exception $e) {
                require __DIR__.'/../src/Views/calendar/eventsListFetchError.php';
            }
        } else {
            require __DIR__.'/../src/Views/calendar/eventsListFetchError.php';
        }
    } catch (\Exception $e) {
        require __DIR__.'/../src/Views/calendar/eventsListFetchError.php';
    }
}