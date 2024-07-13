<?php

namespace App\Auth;

use App\Utils\Redirect;

class Guards
{
    public static function redirectIfAuthenticated() {
        $auth = AuthFactory::getAuth();
        if($auth->isLoggedIn()) {
            Redirect::path('/home.php');
        }
    }

    public static function redirectIfNotAuthenticated() {
        $auth = AuthFactory::getAuth();
        if(!$auth->isLoggedIn()) {
            Redirect::path('/welcome.php');
        }
    }
}