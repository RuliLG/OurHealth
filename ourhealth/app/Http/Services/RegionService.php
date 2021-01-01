<?php

namespace App\Http\Services;

use App\Models\Region;

class RegionService
{
    /**
     * Returns a list of all the regions ordered by name
     */
    public function getAll($country = null)
    {
        $regions = Region::orderBy('name', 'ASC');
        if ($country) {
            $regions->where('country_iso_code', $country);
        }

        return $regions->get();
    }

    /**
     * Gets a region from its identifier
     *
     * @param number $id
     * @return Region
     */
    public function get($id, $fail = false)
    {
        $region = Region::with('hospitals.departments');
        return $fail ? $region->findOrFail($id) : $region->find($id);
    }

    /**
     * Gets a region from its name and country
     *
     * @param number $id
     * @return Region
     */
    public function getFromName($name, $country, $fail = false)
    {
        $region = Region::with('hospitals.departments')
            ->where('name', $name)
            ->where('country_iso_code', $country);
        return $fail ? $region->firstOrFail() : $region->first();
    }
}
