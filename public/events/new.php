<?php
require_once __DIR__.'/../../bootstrap/app.php';

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
require __DIR__.'/../../src/Views/calendar/newEvent.php';

