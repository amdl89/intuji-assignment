<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Auth\Guards;

Guards::redirectIfNotAuthenticated();

$PAGE_TITLE = 'Add event';

$event = [
    'id' => '0',
    'summary' => '',
    'description' => '',
    'start' => [
        'dateTime' => '',
    ],
    'end' => [
        'dateTime' => '',
    ],
    'status' => 'confirmed',
];
$eventFormAction = '/events/create.php';

require __DIR__.'/../../src/Views/calendar/newEvent.php';

