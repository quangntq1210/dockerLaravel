<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\LocaleService;

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

        $payload = $this->localeService->changeLocale(
            $request->input('locale'),
            $request->boolean('withAdminPayload')
        );
        
        return response()->json($payload);
    }
}