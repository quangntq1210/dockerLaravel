<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocaleRequest;
use App\Http\Services\LocaleService;

class LocaleController extends Controller
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function update(UpdateLocaleRequest $request)
    {
        $locale = (string) $request->input('locale');
        $this->localeService->persistLocale($locale);

        if (! $request->expectsJson()) {
            return redirect()->back();
        }

        return response()->json([
            'status' => 'success',
            'lang'   => $this->localeService->getTranslations($locale)
        ]);
    }
}