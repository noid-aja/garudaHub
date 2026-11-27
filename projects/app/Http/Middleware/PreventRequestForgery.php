<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as BaseMiddleware;

class PreventRequestForgery extends BaseMiddleware
{
    protected $addHttpCookie = false;
}
