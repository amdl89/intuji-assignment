<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Auth\Guards;
use App\Services\AuthUserGoogleClientCalendarApiRequestHandler;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;

Guards::redirectIfNotAuthenticated();

if (!isset($_POST['eventId'])) {
    Redirect::path('/home.php');
}

$PAGE_TITLE = 'Edit event';

$requestHandler = AuthUserGoogleClientCalendarApiRequestHandler::getHandler();

$requestHandler->handleApiRequestForAuthUser(
    apiRequestCallback: function ()
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
    },
    refreshTokenRevokedErrorCallback: function () {
        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'error',
            'message' => "Looks like you have been disconnected from your google account. Please connect again.",
        ];
        Redirect::path('/home.php');
    },
    generalErrorCallback: function () {
        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'error',
            'message' => 'Error fetching event.',
        ];
        Redirect::path('/home.php');
    },
);