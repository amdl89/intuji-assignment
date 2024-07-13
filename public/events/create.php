<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Services\AuthUserGoogleClientCalendarApiRequestHandler;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

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

        $service->events->insert('primary', $event);

        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'success',
            'message' => "Event created successfully.",
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
            'message' => 'Error creating event.',
        ];
        Redirect::path('/home.php');
    },
);