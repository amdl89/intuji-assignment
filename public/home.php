<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Auth\AuthFactory;
use App\Auth\Guards;
use App\Services\AuthUserGoogleClientCalendarApiRequestHandler;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;

Guards::redirectIfNotAuthenticated();

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
    $requestHandler = AuthUserGoogleClientCalendarApiRequestHandler::getHandler();

    $requestHandler->handleApiRequestForAuthUser(
        apiRequestCallback: function () {
            $service = GoogleClientFactory::getCalendarServiceForAuthUser();

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
        },
        refreshTokenRevokedErrorCallback: function () {
            $_SESSION['__FLASH_MESSAGE'] = [
                'type' => 'error',
                'message' => "Looks like you have been disconnected from your google account. Please connect again.",
            ];
            Redirect::path('/home.php');
        },
        generalErrorCallback: function () {
            require __DIR__.'/../src/Views/calendar/eventsListFetchError.php';
        },
    );
}