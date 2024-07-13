<?php

use App\Auth\AuthFactory;

require_once __DIR__.'/../bootstrap/app.php';

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
}