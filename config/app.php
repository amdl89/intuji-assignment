<?php
return [
    'db' => [
        'dbUrl' => $_ENV['DB_URL'],
    ],
    'google' => [
        'google_client_id' => $_ENV['GOOGLE_CLIENT_ID'],
        'google_client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
        'google_login_redirect_uri' => $_ENV['LOGIN_REDIRECT_PATH'],
        'google_calendar_redirect_uri' => $_ENV['CALENDAR_REDIRECT_PATH'],
    ]
];