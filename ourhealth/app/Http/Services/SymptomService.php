<?php

namespace App\Http\Services;

use App\Models\Symptom;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class SymptomService
{
    public function getAll()
    {
        return Symptom::orderBy('name', 'ASC')->get();
    }

    public function get($id, $fail = false)
    {
        return $fail ? Symptom::findOrFail($id) : Symptom::find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:180|unique:symptoms,name',
            'description' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $allergy = new Symptom;
        $allergy->name = $data['name'];
        if (isset($data['description'])) {
            $allergy->description_html = $data['description'];
        }

        $allergy->save();

        return $allergy;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:180|unique:symptoms,name,' . $id,
            'description' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $allergy = Symptom::findOrFail($id);
        if (isset($data['name'])) {
            $allergy->name = $data['name'];
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
        $allergy = Symptom::findOrFail($id);
        $allergy->delete();
    }
}
