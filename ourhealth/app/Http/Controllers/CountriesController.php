<?php

namespace App\Http\Controllers;

use App\Http\Services\CountryService;

class CountriesController extends Controller
{
    public function __construct(CountryService $countryService) {
        $this->countryService = $countryService;
    }

    /**
     * Returns a list of all the countries ordered by name
     */
    public function index()
    {
        return response()->json([
            'countries' => $this->countryService->getAll()
        ]);
    }

    /**
     * Gets a country from its iso code
     */
    public function get($isoCode)
    {
        return response()->json($this->countryService->get($isoCode, true));
    }
}
