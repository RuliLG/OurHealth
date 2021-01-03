<?php

namespace App\Http\Services;

use App\Models\Measurement;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class MeasurementService
{
    /**
     * Returns a list of all the measurements from a patient
     *
     * @return array<Hospital>
     */
    public function getFromPatient(Patient $patient)
    {
        return $patient->measurements;
    }

    public function getFromBatchId(Patient $patient, $batchId)
    {
        return $patient
            ->measurements()
            ->where('batch_id', $batchId)
            ->get();
    }

    /**
     * Creates a Measurement list
     *
     * @return array<Measurement>
     */
    public function store($data)
    {
        $validator = Validator::make($data, [
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'sometimes|nullable|exists:visits,id',
            'measurements' => 'required|array',
            'measurements.*.type' => 'required|in:vitals,triage,heart,blood,urine,other',
            'measurements.*.other_type' => 'required_if:measurements.*.type,other',
            'measurements.*.name' => 'required|string|max:255',
            'measurements.*.value' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $batchId = Measurement::where('patient_id', $data['patient_id'])->max('batch_id') + 1;
        $records = [];
        DB::beginTransaction();
        foreach ($data['measurements'] as $measurement) {
            $record = new Measurement;
            $record->patient_id = $data['patient_id'];
            $record->visit_id = $data['visit_id'];
            $record->type = $measurement['type'];
            $record->other_type = $measurement['other_type'];
            $record->name = $measurement['name'];
            $record->value = $measurement['value'];
            $record->batch_id = $batchId;
            $record->save();
            $records[] = $record;
        }
        DB::commit();
        return $records;
    }

    /**
     * Removes a hospital from its id
     *
     * @param string $id
     * @param boolean $fail if the function should abort with a 404 code if the hospital is not found
     * @return void
     */
    public function destroy($id)
    {
        $measurement = Measurement::findOrFail($id);
        $measurement->delete();
    }
}
