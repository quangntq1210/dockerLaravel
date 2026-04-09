<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Services\LocaleService;

class LocaleController extends Controller
{
    protected $localeService;
    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function update(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:vi,en'
        ]);
        $result = $this->localeService->updateLocale($request->locale);

        return response()->json($result);
    }
}