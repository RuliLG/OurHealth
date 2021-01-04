<?php

namespace App\Http\Services;

use App\Models\Measurement;
use App\Models\Visit;
use App\Models\VisitDiagnosis;
use App\Models\VisitSymptom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class VisitService
{
    public function getAll($data = [])
    {
        $validator = Validator::make($data, [
            'patient' => 'sometimes|nullable|exists:patients,id',
            'hospital' => 'sometimes|nullable|exists:hospitals,id',
            'doctor' => 'sometimes|nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $visits = Visit::where('id', '>', 0);
        if (isset($data['patient'])) {
            $visits->where('patient_id', $data['patient']);
        }

        if (isset($data['hospital'])) {
            $visits->where('hospital_id', $data['hospital']);
        }

        if (isset($data['doctor'])) {
            $visits->where('doctor_id', $data['doctor']);
        }

        return $visits->orderBy('created_at', 'DESC')->get();
    }

    public function get($id, $fail = false)
    {
        $visit = Visit::with('appointment', 'patient', 'hospital', 'doctor', 'conditions', 'allergies', 'symptoms', 'files', 'reports');
        $visit = $fail ? $visit->findOrFail($id) : $visit->find($id);
        $visit->load([
            'vitals' => function ($query) use ($visit) {
                $query->where('patient_id', $visit->patient_id)
                    ->where('batch_id', $visit->vitals_batch_id);
            },
            'triage' => function ($query) use ($visit) {
                $query->where('patient_id', $visit->patient_id)
                    ->where('batch_id', $visit->triage_batch_id);
            }
        ]);
        return $visit;
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'patient' => 'required|exists:patients,id',
            'hospital' => 'required|exists:hospitals,id',
            'doctor' => 'required|exists:users,id',
            'appointment' => 'sometimes|nullable|exists:appointments,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $visit = new Visit;
        $visit->patient_id = $data['patient'];
        $visit->hospital_id = auth()->user()->is_superadmin ? $data['hospital'] : auth()->user()->hospital_id;
        $visit->doctor_id = auth()->user()->is_superadmin ? $data['doctor'] : auth()->user()->id;
        $visit->started_at = now();

        if (isset($data['appointment'])) {
            $visit->appointment_id = $data['appointment'];
        }
        $visit->save();

        return $this->get($visit->id);
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'category' => 'sometimes|in:urgent,emergency,non-urgent',
            'vitals_batch_id' => 'sometimes|exists:measurements,batch_id',
            'triage_batch_id' => 'sometimes|exists:measurements,batch_id',
            'history' => 'sometimes|string',
            'observations' => 'sometimes|string',
            'diagnoses' => 'sometimes|array',
            'diagnoses.*.condition' => 'sometimes|exists:conditions,id|required_without:diagnoses.*.allergy',
            'diagnoses.*.allergy' => 'sometimes|exists:allergies,id|required_without:diagnoses.*.condition',
            'diagnoses.*.comments' => 'sometimes|string',
            'symptoms' => 'sometimes|array',
            'symptoms.*' => 'sometimes|exists:symptoms,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $visit = Visit::findOrFail($id);
        DB::beginTransaction();
        if (isset($data['category'])) {
            $visit->category = $data['category'];
        }

        if (isset($data['vitals_batch_id'])) {
            $batch = Measurement::where([
                'batch_id' => $data['vitals_batch_id'],
                'patient_id' => $visit->patient_id
            ])->first();
            if ($batch) {
                $visit->vitals_batch_id = $data['vitals_batch_id'];
            }
        }

        if (isset($data['triage_batch_id'])) {
            $batch = Measurement::where([
                'batch_id' => $data['triage_batch_id'],
                'patient_id' => $visit->patient_id
            ])->first();
            if ($batch) {
                $visit->triage_batch_id = $data['triage_batch_id'];
            }
        }

        if (isset($data['history'])) {
            $visit->history = $data['history'];
        }

        if (isset($data['observations'])) {
            $visit->observations = $data['observations'];
        }

        if ($visit->isDirty()) {
            $visit->save();

            if ($visit->triage_batch_id) {
                Measurement::where([
                    'batch_id' => $visit->triage_batch_id,
                    'patient_id' => $visit->patient_id
                ])->update([
                    'visit_id' => $visit->id
                ]);
            }

            if ($visit->vitals_batch_id) {
                Measurement::where([
                    'batch_id' => $visit->vitals_batch_id,
                    'patient_id' => $visit->patient_id
                ])->update([
                    'visit_id' => $visit->id
                ]);
            }
        }

        if (isset($data['diagnoses'])) {
            foreach ($data['diagnoses'] as $diagnosis) {
                $this->linkDiagnosis($visit, $diagnosis);
            }
        }

        if (isset($data['symptoms'])) {
            foreach ($data['symptoms'] as $symptomId) {
                $this->linkSymptom($visit, $symptomId);
            }
        }

        DB::commit();

        return $this->get($visit->id);
    }

    public function linkDiagnosis(Visit $visit, $diagnosis)
    {
        $where = [
            'visit_id' => $visit->id
        ];
        if (isset($diagnosis['condition'])) {
            $where['condition_id'] = $diagnosis['condition'];
        }

        if (isset($diagnosis['allergy'])) {
            $where['allergy_id'] = $diagnosis['allergy'];
        }

        $data = [
            'comments' => $diagnosis['comments'] ?? null
        ];
        VisitDiagnosis::updateOrCreate($where, $data);
    }

    public function unlinkDiagnosis(Visit $visit, $diagnosis)
    {
        $where = [
            'visit_id' => $visit->id
        ];
        if (isset($diagnosis['condition'])) {
            $where['condition_id'] = $diagnosis['condition'];
        }

        if (isset($diagnosis['allergy'])) {
            $where['allergy_id'] = $diagnosis['allergy'];
        }

        if (!isset($where['allergy_id']) && !isset($where['condition_id'])) {
            return;
        }

        VisitDiagnosis::where($where)->delete();
    }

    public function linkSymptom(Visit $visit, $symptomId)
    {
        VisitSymptom::firstOrCreate([
            'visit_id' => $visit->id,
            'symptom_id' => $symptomId
        ]);
    }

    public function unlinkSymptom(Visit $visit, $symptomId)
    {
        VisitSymptom::where([
            'visit_id' => $visit->id,
            'symptom_id' => $symptomId
        ])->delete();
    }

    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->delete();
    }
}
