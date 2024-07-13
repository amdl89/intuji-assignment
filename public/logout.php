<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Utils\Redirect;

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

Redirect::path('/welcome.php');