<?php

namespace App\Http\Services;

use App\Models\Hospital;
use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class HospitalService
{
    /**
     * Returns a list of all the hospitals ordered by name
     *
     * @return array<Hospital>
     */
    public function getAll($options = [])
    {
        $validator = Validator::make($options, [
            'country' => 'sometimes|exists:countries,iso_code',
            'region' => 'sometimes|exists:regions,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $hospitals = Hospital::orderBy('name', 'ASC');
        if (isset($options['country'])) {
            $regions = Region::select('id')
                ->where('country_iso_code', $options['country'])
                ->pluck('id');
            $hospitals->whereIn('region_id', $regions->isEmpty() ? [-1] : $regions);
        }

        if (isset($options['region'])) {
            $hospitals->where('region_id', $options['region']);
        }

        return $hospitals->get();
    }

    /**
     * Gets a hospital from its id
     *
     * @param string $id
     * @param boolean $fail if the function should abort with a 404 code if the hospital is not found
     * @return Hospital
     */
    public function get($id, $fail = false)
    {
        $hospital = Hospital::with('departments');
        return $fail ? $hospital->findOrFail($id) : $hospital->find($id);
    }

    /**
     * Creates a hospital
     *
     * @return Hospital
     */
    public function store($data)
    {
        $validator = Validator::make($data, [
            'region' => 'required|exists:regions,id',
            'name' => 'required|string|max:60',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $hospital = new Hospital;
        $hospital->region_id = $data['region'];
        $hospital->name = $data['name'];
        $hospital->save();
        return $hospital;
    }

    /**
     * Updates a hospital
     *
     * @return Hospital
     */
    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'region' => 'sometimes|exists:regions,id',
            'name' => 'sometimes|string|max:60',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $hospital = Hospital::findOrFail($id);
        if (isset($data['region'])) {
            $hospital->region_id = $data['region'];
        }

        if (isset($data['name'])) {
            $hospital->name = $data['name'];
        }

        if ($hospital->isDirty()) {
            $hospital->save();
        }

        return $hospital;
    }

    /**
     * Removes a hospital from its id
     *
     * @param string $id
     * @param boolean $fail if the function should abort with a 404 code if the hospital is not found
     * @return void
     */
    public function destroy($id, $fail = false)
    {
        if ($fail) {
            $hospital = Hospital::findOrFail($id);
            $hospital->delete();
        } else {
            Hospital::destroy($id);
        }
    }
}
