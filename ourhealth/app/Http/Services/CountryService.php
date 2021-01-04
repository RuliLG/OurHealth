<?php

namespace App\Http\Services;

use App\Models\Country;

class CountryService
{
    public function getAll()
    {
        return Country::orderBy('name', 'ASC')
            ->get();
    }

    public function get($isoCode, $fail = false)
    {
        $country = Country::with('regions')
            ->where('iso_code', mb_strtolower($isoCode));
        return $fail ? $country->firstOrFail() : $country->first();
    }
}
