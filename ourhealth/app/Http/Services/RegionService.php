<?php

namespace App\Http\Services;

use App\Models\Region;

class RegionService
{
    public function getAll($country = null)
    {
        $regions = Region::orderBy('name', 'ASC');
        if ($country) {
            $regions->where('country_iso_code', $country);
        }

        return $regions->get();
    }

    public function get($id, $fail = false)
    {
        $region = Region::with('hospitals.departments');
        return $fail ? $region->findOrFail($id) : $region->find($id);
    }

    public function getFromName($name, $country, $fail = false)
    {
        $region = Region::with('hospitals.departments')
            ->where('name', $name)
            ->where('country_iso_code', $country);
        return $fail ? $region->firstOrFail() : $region->first();
    }
}
