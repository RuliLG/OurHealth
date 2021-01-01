<?php

namespace App\Http\Services;

use App\Models\Country;

class CountryService
{
    /**
     * Returns a list of all the countries ordered by name
     *
     * @return array<Country>
     */
    public function getAll()
    {
        return Country::orderBy('name', 'ASC')
            ->get();
    }

    /**
     * Gets a country from its iso code
     *
     * @param string $isoCode
     * @param boolean $fail if the function should abort with a 404 code if the iso code is not valid
     * @return Country
     */
    public function get($isoCode, $fail = false)
    {
        $country = Country::with('regions')
            ->where('iso_code', mb_strtolower($isoCode));
        return $fail ? $country->firstOrFail() : $country->first();
    }
}
