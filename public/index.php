<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Utils\Redirect;

$PAGE_TITLE = 'Calendar Plugin';

Redirect::path('/welcome.php');