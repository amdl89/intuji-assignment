<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Auth\Guards;

$PAGE_TITLE = 'Welcome';

Guards::redirectIfAuthenticated();

require __DIR__ . '/../src/Views/welcome.php';