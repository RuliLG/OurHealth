<?php

namespace App\Http\Services;

use App\Models\Allergy;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class AllergyService
{
    public function getAll()
    {
        return Allergy::orderBy('name', 'ASC')->get();
    }

    public function get($id, $fail = false)
    {
        return $fail ? Allergy::findOrFail($id) : Allergy::find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:180|unique:allergies,name',
            'severity' => 'required|string|in:none,low,mid,high',
            'description' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $allergy = new Allergy;
        $allergy->name = $data['name'];
        $allergy->severity = $data['severity'];
        if (isset($data['description'])) {
            $allergy->description_html = $data['description'];
        }

        $allergy->save();

        return $allergy;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:180|unique:allergies,name,' . $id,
            'severity' => 'sometimes|nullable|string|in:none,low,mid,high',
            'description' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $allergy = Allergy::findOrFail($id);
        if (isset($data['name'])) {
            $allergy->name = $data['name'];
        }

        if (isset($data['severity'])) {
            $allergy->severity = $data['severity'];
        }

        if (isset($data['description'])) {
            $allergy->description_html = $data['description'];
        }

        if ($allergy->isDirty()) {
            $allergy->save();
        }

        return $allergy;
    }

    public function destroy($id)
    {
        $allergy = Allergy::findOrFail($id);
        $allergy->delete();
    }
}
