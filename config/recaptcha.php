<?php

/*
|--------------------------------------------------------------------------
| Google ReCaptcha
|--------------------------------------------------------------------------
*/

return [

    'enabled'     => env('RECAPTCHA_ENABLED', true),

    'key'         => env('RECAPTCHA_SITE_KEY'),

    'secret'      => env('RECAPTCHA_SECRET_KEY'),

    'verify_uri'  => 'https://www.google.com/recaptcha/api/siteverify'

];