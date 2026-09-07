<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->header('Accept-Language')
            ?? Config::get('app.locale', 'en');

        $locale = in_array($locale, Config::get('app.available_locales', ['en', 'ar']))
            ? $locale
            : 'en';

        App::setLocale($locale);

        $direction = Config::get('app.locale_direction.' . $locale, 'ltr');
        Config::set('app.locale_direction', $direction);

        return $next($request);
    }
}
