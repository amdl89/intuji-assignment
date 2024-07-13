<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Services\AuthUserGoogleClientCalendarApiRequestHandler;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

if(!isset($_POST['eventId'])) {
    Redirect::path('/home.php');
}

$requestHandler = AuthUserGoogleClientCalendarApiRequestHandler::getHandler();

$requestHandler->handleApiRequestForAuthUser(
    apiRequestCallback: function () {
        $service = GoogleClientFactory::getCalendarServiceForAuthUser();

        $event = new Event([
            'summary' => $_POST['summary'],
            'status' => $_POST['status'] ?: null,
            'description' => $_POST['description'],
            'start' => new EventDateTime([
                'dateTime' => date('Y-m-d\TH:i:s', strtotime($_POST['timeMin'])),
                'timeZone' => 'UTC',
            ]),
            'end' => new EventDateTime([
                'dateTime' => date('Y-m-d\TH:i:s', strtotime($_POST['timeMax'])),
                'timeZone' => 'UTC',
            ]),
        ]);

        $service->events->update('primary', $_POST['eventId'], $event);

        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'success',
            'message' => "Event updated successfully.",
        ];

        Redirect::path('/home.php');
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
            'message' => 'Error updating event.',
        ];
        Redirect::path('/home.php');
    },
);