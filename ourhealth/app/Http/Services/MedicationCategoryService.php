<?php

namespace App\Http\Services;

use App\Models\MedicationCategory;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class MedicationCategoryService
{
    public function getAll()
    {
        return MedicationCategory::orderBy('name', 'ASC')->get();
    }

    public function getAllWithMedications()
    {
        return MedicationCategory::with('medications.allergies', 'medications.conditions', 'medications.incompatibilities')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function get($id, $fail = false)
    {
        $category = MedicationCategory::with('medications.allergies', 'medications.conditions', 'medications.incompatibilities');
        return $fail ? $category->findOrFail($id) : $category->find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:180|unique:medication_categories,name'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $category = new MedicationCategory;
        $category->name = $data['name'];
        $category->save();

        return $category;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:180|unique:medication_categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $category = MedicationCategory::findOrFail($id);
        if (isset($data['name'])) {
            $category->name = $data['name'];
        }

        if ($category->isDirty()) {
            $category->save();
        }

        return $category;
    }

    public function destroy($id)
    {
        $category = MedicationCategory::findOrFail($id);
        $category->delete();
    }
}
