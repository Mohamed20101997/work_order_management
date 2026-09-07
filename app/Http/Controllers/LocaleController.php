<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;

class LocaleController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->validate([
            'locale' => 'required|in:en,ar',
        ])['locale'];

        $request->session()->put('locale', $locale);

        return Redirect::back();
    }
}
