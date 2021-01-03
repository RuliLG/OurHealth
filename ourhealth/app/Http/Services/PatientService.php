<?php

namespace App\Http\Services;

use App\Models\Medication;
use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\PatientCondition;
use App\Models\PatientMedication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PatientService
{
    public function getAll()
    {
        return Patient::orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();
    }

    public function get($id, $fail = false)
    {
        $patient = Patient::with('country', 'region', 'preferred_hospital', 'third_party_insurance', 'doctor', 'biological_father', 'biological_mother', 'files', 'allergies', 'conditions', 'medications');
        return $fail ? $patient->findOrFail($id) : $patient->find($id);
    }

    public function getByCountryId($isoCode, $id, $fail = false)
    {
        $patient = Patient::with('country', 'region', 'preferred_hospital', 'third_party_insurance', 'doctor', 'biological_father', 'biological_mother', 'files', 'allergies', 'conditions', 'medications')
            ->where([
                'nationality' => $isoCode,
                'id_card' => $id
            ]);
        return $fail ? $patient->firstOrFail() : $patient->first();
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:60',
            'middle_name' => 'sometimes|nullable|string|max:60',
            'last_name' => 'sometimes|nullable|string|max:60',
            'id_card' => 'required|string|max:120',
            'nationality' => 'required|string|exists:countries,iso_code',
            'region' => 'required|exists:regions,id',
            'photo' => 'sometimes|nullable|file|image|dimensions:min_width=150,min_height=150',
            'hospital' => 'required|exists:hospitals,id',
            'date_of_birth' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'phone_number' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email:rfc,dns',
            'blood_type' => 'sometimes|nullable|in:A+,B+,A-,B-,AB-,AB+,0-,0+',
            'third_party_insurance' => 'sometimes|nullable|exists:third_party_insurances,id',
            'doctor' => 'sometimes|nullable|exists:users,id',
            'biological_father' => 'sometimes|nullable|exists:patients,id',
            'biological_mother' => 'sometimes|nullable|exists:patients,id',
            'medications' => 'sometimes|nullable|array',
            'medications.*.id' => 'sometimes|nullable|exists:medications,id',
            'medications.*.diagnosis' => 'sometimes|nullable|exists:visit_diagnoses,id',
            'medications.*.start_date' => 'sometimes|nullable|date|date_format:Y-m-d',
            'medications.*.end_date' => 'sometimes|nullable|date|date_format:Y-m-d',
            'medications.*.frequency_type' => 'in:hourly,daily,weekly,monthly,yearly',
            'medications.*.frequency' => 'integer|min:1',
            'medications.*.amount' => 'sometimes|nullable|string',
            'conditions' => 'sometimes|nullable|array',
            'conditions.*' => 'sometimes|nullable|exists:conditions,id',
            'allergies' => 'sometimes|nullable|array',
            'allergies.*' => 'sometimes|nullable|exists:allergies,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $patient = $this->getByCountryId($data['nationality'], $data['id_card']);
        if ($patient) {
            throw new InvalidParameterException('Patient already exists');
        }

        DB::beginTransaction();
        $patient = new Patient;
        $patient->first_name = $data['first_name'];
        $patient->id_card = $data['id_card'];
        $patient->nationality = $data['nationality'];
        $patient->region_id = $data['region'];
        $patient->preferred_hospital_id = $data['hospital'];
        $patient->date_of_birth = $data['date_of_birth'];
        if (isset($data['middle_name'])) {
            $patient->middle_name = $data['middle_name'];
        }

        if (isset($data['last_name'])) {
            $patient->last_name = $data['last_name'];
        }

        if (isset($data['photo'])) {
            $s3Key = (new FileService)->upload($data['photo'], 'patients');
            $patient->photo_s3_key = $s3Key;
        }

        if (isset($data['phone_number'])) {
            $patient->phone_number = $data['phone_number'];
        }

        if (isset($data['email'])) {
            $patient->email = $data['email'];
        }

        if (isset($data['blood_type'])) {
            $patient->blood_type = $data['blood_type'];
        }

        if (isset($data['third_party_insurance'])) {
            $patient->third_party_insurance_id = $data['third_party_insurance'];
        }

        if (isset($data['doctor'])) {
            $patient->doctor_id = $data['doctor'];
        }

        if (isset($data['biological_father'])) {
            $patient->biological_father_id = $data['biological_father'];
        }

        if (isset($data['biological_mother'])) {
            $patient->biological_mother_id = $data['biological_mother'];
        }

        $patient->save();

        if (isset($data['allergies'])) {
            foreach ($data['allergies'] as $allergyId) {
                $this->linkAllergy($patient, $allergyId);
            }
        }

        if (isset($data['conditions'])) {
            foreach ($data['conditions'] as $conditionId) {
                $this->linkCondition($patient, $conditionId);
            }
        }

        if (isset($data['medications'])) {
            foreach ($data['medications'] as $medication) {
                $this->linkMedication($patient, $medication);
            }
        }

        DB::commit();

        return $this->get($patient->id);
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'sometimes|nullable|string|max:60',
            'middle_name' => 'sometimes|nullable|string|max:60',
            'last_name' => 'sometimes|nullable|string|max:60',
            'id_card' => 'sometimes|nullable|string|max:120',
            'nationality' => 'sometimes|nullable|string|exists:countries,iso_code',
            'region' => 'sometimes|nullable|exists:regions,id',
            'photo' => 'sometimes|nullable|file|image|dimensions:min_width=150,min_height=150',
            'hospital' => 'sometimes|nullable|exists:hospitals,id',
            'date_of_birth' => 'sometimes|nullable|date|date_format:Y-m-d|before_or_equal:today',
            'phone_number' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email:rfc,dns',
            'blood_type' => 'sometimes|nullable|in:A+,B+,A-,B-,AB-,AB+,0-,0+',
            'third_party_insurance' => 'sometimes|nullable|exists:third_party_insurances,id',
            'doctor' => 'sometimes|nullable|exists:users,id',
            'biological_father' => 'sometimes|nullable|exists:patients,id',
            'biological_mother' => 'sometimes|nullable|exists:patients,id',
            'medications' => 'sometimes|nullable|array',
            'medications.*.id' => 'sometimes|nullable|exists:medications,id',
            'medications.*.diagnosis' => 'sometimes|nullable|exists:visit_diagnoses,id',
            'medications.*.start_date' => 'sometimes|nullable|date|date_format:Y-m-d',
            'medications.*.end_date' => 'sometimes|nullable|date|date_format:Y-m-d',
            'medications.*.frequency_type' => 'in:hourly,daily,weekly,monthly,yearly',
            'medications.*.frequency' => 'integer|min:1',
            'medications.*.amount' => 'sometimes|nullable|string',
            'conditions' => 'sometimes|nullable|array',
            'conditions.*' => 'sometimes|nullable|exists:conditions,id',
            'allergies' => 'sometimes|nullable|array',
            'allergies.*' => 'sometimes|nullable|exists:allergies,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $patient = Patient::findOrFail($id);

        DB::beginTransaction();
        if (isset($data['first_name'])) {
            $patient->first_name = $data['first_name'];
        }

        if (isset($data['id_card'])) {
            $patient->id_card = $data['id_card'];
        }

        if (isset($data['nationality'])) {
            $patient->nationality = $data['nationality'];
        }
        if (isset($data['region'])) {
            $patient->region_id = $data['region'];
        }
        if (isset($data['hospital'])) {
            $patient->preferred_hospital_id = $data['hospital'];
        }
        if (isset($data['date_of_birth'])) {
            $patient->date_of_birth = $data['date_of_birth'];
        }
        if (isset($data['middle_name'])) {
            $patient->middle_name = $data['middle_name'];
        }

        if (isset($data['last_name'])) {
            $patient->last_name = $data['last_name'];
        }

        if (isset($data['photo'])) {
            $s3Key = (new FileService)->upload($data['photo'], 'patients');
            $patient->photo_s3_key = $s3Key;
        }

        if (isset($data['phone_number'])) {
            $patient->phone_number = $data['phone_number'];
        }

        if (isset($data['email'])) {
            $patient->email = $data['email'];
        }

        if (isset($data['blood_type'])) {
            $patient->blood_type = $data['blood_type'];
        }

        if (isset($data['third_party_insurance'])) {
            $patient->third_party_insurance_id = $data['third_party_insurance'];
        }

        if (isset($data['doctor'])) {
            $patient->doctor_id = $data['doctor'];
        }

        if (isset($data['biological_father'])) {
            $patient->biological_father_id = $data['biological_father'];
        }

        if (isset($data['biological_mother'])) {
            $patient->biological_mother_id = $data['biological_mother'];
        }

        $patient->save();

        if (isset($data['allergies'])) {
            foreach ($data['allergies'] as $allergyId) {
                $this->linkAllergy($patient, $allergyId);
            }
        }

        if (isset($data['conditions'])) {
            foreach ($data['conditions'] as $conditionId) {
                $this->linkCondition($patient, $conditionId);
            }
        }

        if (isset($data['medications'])) {
            foreach ($data['medications'] as $medication) {
                $this->linkMedication($patient, $medication);
            }
        }

        DB::commit();

        return $this->get($patient->id);
    }

    public function destroy($id)
    {
        $category = Patient::findOrFail($id);
        $category->delete();
    }

    public function linkAllergy(Patient $patient, $allergyId) {
        PatientAllergy::firstOrCreate([
            'patient_id' => $patient->id,
            'allergy_id' => $allergyId
        ]);
    }

    public function unlinkAllergy(Patient $patient, $allergyId) {
        PatientAllergy::where([
            'patient_id' => $patient->id,
            'allergy_id' => $allergyId
        ])->delete();
    }

    public function linkCondition(Patient $patient, $conditionId) {
        PatientCondition::firstOrCreate([
            'patient_id' => $patient->id,
            'condition_id' => $conditionId
        ]);
    }

    public function unlinkCondition(Patient $patient, $conditionId) {
        PatientCondition::where([
            'patient_id' => $patient->id,
            'condition_id' => $conditionId
        ])->delete();
    }

    public function linkMedication(Patient $patient, $medication) {
        $patientMedication = new PatientMedication;
        $patientMedication->patient_id = $patient->id;
        $patientMedication->medication_id = $medication['id'];
        $patientMedication->visit_diagnosis_id = $medication['diagnosis'] ?? null;
        $patientMedication->start_date = $medication['start_date'] ?? now();
        $patientMedication->end_date = $medication['end_date'] ?? null;
        $patientMedication->frequency_type = $medication['frequency_type'];
        $patientMedication->frequency = $medication['frequency'];
        $patientMedication->amount = $medication['amount'];
        $patientMedication->save();
    }

    public function unlinkMedication(Patient $patient, $id) {
        PatientMedication::where([
            'patient_id' => $patient->id,
            'id' => $id
        ])->delete();
    }
}
