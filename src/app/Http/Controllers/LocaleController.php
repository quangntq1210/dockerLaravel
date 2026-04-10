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
        $this->localeService->persistLocale($request->locale);

        return response()->json([
            'status' => 'success',
            'lang'   => $this->localeService->getTranslations($request->locale)
        ]);
    }
}