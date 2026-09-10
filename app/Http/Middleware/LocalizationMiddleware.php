<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = config('app.available_locales', ['en', 'ar']);

        $locale = $request->session()->get('locale')
            ?? $request->user()?->locale
            ?? str_replace('-', '_', (string) ($request->getPreferredLanguage($available) ?: config('app.locale', 'en')));

        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);
        \Illuminate\Support\Carbon::setLocale($locale);

        return $next($request);
    }
}
