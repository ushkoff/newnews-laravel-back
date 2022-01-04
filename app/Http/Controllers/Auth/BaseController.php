<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * @abstract Class BaseController
 * @package App\Http\Controllers\Auth
 *
 * Базовый контроллер для пространства имен Auth.
 * Здесь могут быть определены методы, которые получат все "последователи" данного класса.
 */
abstract class BaseController extends Controller
{
    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        //
    }
}
