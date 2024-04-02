<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;
use Session;

class LocalizationController extends Controller
{
    public function setLocale($locale)
    {
        if (in_array($locale, ['en', 'tl'])) {
            Session::put('locale', $locale);
        }
        return response([
            'data'=> $locale
        ]);
    }

    public function getTranslations(Request $request)
    {
        $lang = $request->input('lang');

        // Fetch translations for the requested language from the database
        $translations = Translation::where('lang', $lang)->get();

        return response()->json($translations);
    }
}
