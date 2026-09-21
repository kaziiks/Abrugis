<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', 'lv');
        app()->setLocale(in_array($locale, ['lv', 'en', 'ru'], true) ? $locale : 'lv');

        return $next($request);
    }
}