<?php

namespace App\Http\Services;

use App\Models\Medication;
use App\Models\MedicationAllergy;
use App\Models\MedicationCondition;
use App\Models\MedicationIncompatibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class MedicationService
{
    public function getAll()
    {
        return Medication::orderBy('commercial_name', 'ASC')->get();
    }

    public function get($id, $fail = false)
    {
        $medication = Medication::with('category', 'allergies', 'conditions', 'incompatibilities');
        return $fail ? $medication->findOrFail($id) : $medication->find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'commercial_name' => 'required|string|max:180|unique:medications,commercial_name',
            'active_ingredient' => 'required|string|max:180',
            'kind' => 'required|in:capsule,tablet,inhaler,sachet,needle,cure,intravenous,gel,spray,mousse,syrup',
            'category' => 'required|exists:medication_categories,id',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'sometimes|exists:allergies,id',
            'conditions' => 'sometimes|array',
            'conditions.*' => 'sometimes|exists:conditions,id',
            'incompatibilities' => 'sometimes|array',
            'incompatibilities.*' => 'sometimes|exists:medications,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        DB::beginTransaction();
        $medication = new Medication;
        $medication->commercial_name = $data['commercial_name'];
        $medication->active_ingredient = $data['active_ingredient'];
        $medication->kind = $data['kind'];
        $medication->medication_category_id = $data['category'];
        $medication->save();

        if (isset($data['allergies'])) {
            foreach ($data['allergies'] as $allergyId) {
                $this->linkAllergy($medication, $allergyId);
            }
        }

        if (isset($data['conditions'])) {
            foreach ($data['conditions'] as $conditionId) {
                $this->linkCondition($medication, $conditionId);
            }
        }

        if (isset($data['incompatibilities'])) {
            $medications = Medication::whereIn('id', $data['incompatibilities']);
            foreach ($medications as $incompatible) {
                $this->linkIncompatibility($medication, $incompatible);
            }
        }
        DB::commit();

        return $this->get($medication->id);
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'commercial_name' => 'sometimes|string|max:180|unique:medications,commercial_name,' . $id,
            'active_ingredient' => 'sometimes|string|max:180',
            'kind' => 'sometimes|in:capsule,tablet,inhaler,sachet,needle,cure,intravenous,gel,spray,mousse,syrup',
            'category' => 'sometimes|exists:medication_categories,id',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'sometimes|exists:allergies,id',
            'conditions' => 'sometimes|array',
            'conditions.*' => 'sometimes|exists:conditions,id',
            'incompatibilities' => 'sometimes|array',
            'incompatibilities.*' => 'sometimes|exists:medications,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        DB::beginTransaction();
        $medication = Medication::findOrFail($id);
        if (isset($data['commercial_name'])) {
            $medication->commercial_name = $data['commercial_name'];
        }

        if (isset($data['active_ingredient'])) {
            $medication->active_ingredient = $data['active_ingredient'];
        }

        if (isset($data['kind'])) {
            $medication->kind = $data['kind'];
        }

        if (isset($data['category'])) {
            $medication->medication_category_id = $data['category'];
        }

        if (isset($data['allergies'])) {
            foreach ($data['allergies'] as $allergyId) {
                $this->linkAllergy($medication, $allergyId);
            }
        }

        if (isset($data['conditions'])) {
            foreach ($data['conditions'] as $conditionId) {
                $this->linkCondition($medication, $conditionId);
            }
        }

        if (isset($data['incompatibilities'])) {
            $medications = Medication::whereIn('id', $data['incompatibilities'])->get();
            foreach ($medications as $incompatible) {
                $this->linkIncompatibility($medication, $incompatible);
            }
        }

        if ($medication->isDirty()) {
            $medication->save();
        }

        DB::commit();

        return $this->get($medication->id);
    }

    public function destroy($id)
    {
        $category = Medication::findOrFail($id);
        $category->delete();
    }

    public function linkAllergy(Medication $medication, $allergyId) {
        MedicationAllergy::firstOrCreate([
            'medication_id' => $medication->id,
            'allergy_id' => $allergyId
        ]);
    }

    public function unlinkAllergy(Medication $medication, $allergyId) {
        MedicationAllergy::where([
            'medication_id' => $medication->id,
            'allergy_id' => $allergyId
        ])->delete();
    }

    public function linkCondition(Medication $medication, $conditionId) {
        MedicationCondition::firstOrCreate([
            'medication_id' => $medication->id,
            'condition_id' => $conditionId
        ]);
    }

    public function unlinkCondition(Medication $medication, $conditionId) {
        MedicationCondition::where([
            'medication_id' => $medication->id,
            'condition_id' => $conditionId
        ])->delete();
    }

    public function linkIncompatibility(Medication $medicationA, Medication $medicationB) {
        if ($medicationA->id === $medicationB->id) {
            return;
        }

        MedicationIncompatibility::firstOrCreate([
            'medication_id' => $medicationA->id,
            'incompatible_with' => $medicationB->id
        ]);
        MedicationIncompatibility::firstOrCreate([
            'medication_id' => $medicationB->id,
            'incompatible_with' => $medicationA->id
        ]);
    }

    public function unlinkIncompatibility(Medication $medicationA, Medication $medicationB) {
        MedicationIncompatibility::where([
            'medication_id' => $medicationA->id,
            'incompatible_with' => $medicationB->id
        ])
            ->orWhere([
                'medication_id' => $medicationB->id,
                'incompatible_with' => $medicationA->id
            ])
            ->delete();
    }
}
