<?php

namespace App\Http\Services;

use App\Models\Hospital;
use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class HospitalService
{
    public function getAll($options = [])
    {
        $validator = Validator::make($options, [
            'country' => 'sometimes|nullable|exists:countries,iso_code',
            'region' => 'sometimes|nullable|exists:regions,id'
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

    public function get($id, $fail = false)
    {
        $hospital = Hospital::with('region', 'departments');
        return $fail ? $hospital->findOrFail($id) : $hospital->find($id);
    }

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

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'region' => 'sometimes|nullable|exists:regions,id',
            'name' => 'sometimes|nullable|string|max:60',
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
