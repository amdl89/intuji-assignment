<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Services\AuthUserGoogleClientCalendarApiRequestHandler;
use App\Services\GoogleClientFactory;
use App\Utils\Redirect;

if(!isset($_POST['eventId'])) {
    Redirect::path('/home.php');
}

$requestHandler = AuthUserGoogleClientCalendarApiRequestHandler::getHandler();

$requestHandler->handleApiRequestForAuthUser(
    apiRequestCallback: function () {
        $service = GoogleClientFactory::getCalendarServiceForAuthUser();

        $service->events->delete('primary', $_POST['eventId']);

        $_SESSION['__FLASH_MESSAGE'] = [
            'type' => 'success',
            'message' => "Event deleted successfully.",
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
            'message' => 'Error deleting event.',
        ];
        Redirect::path('/home.php');
    },
);