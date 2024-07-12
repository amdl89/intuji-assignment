<?php

namespace App\Utils;

class Redirect
{
    public static function path($path) {
        header('Location: ' . $path);
        exit();
    }
}