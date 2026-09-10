<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->route('locale');

        abort_unless(
            in_array($locale, config('app.available_locales', ['en', 'ar']), true),
            404,
        );

        $request->session()->put('locale', $locale);

        $request->user()?->update(['locale' => $locale]);

        return redirect()->back();
    }
}
