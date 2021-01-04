<?php

namespace App\Http\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class AppointmentService
{
    public function getAll($data = [])
    {
        $validator = Validator::make($data, [
            'patient' => 'sometimes|exists:patients,id',
            'status' => 'sometimes|in:pending,confirmed,cancelled,success',
            'hospital' => 'sometimes|exists:hospitals,id',
            'department' => 'sometimes|exists:hospital_departments,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $appointments = Appointment::where('id', '>', 0);
        // TODO: Check if the current user has permissions to see this patient's data
        if (isset($data['patient'])) {
            $appointments->where('patient_id', $data['patient']);
        }

        if (isset($data['status'])) {
            $appointments->where('status', $data['status']);
        }

        if (isset($data['hospital'])) {
            $appointments->where('hospital_id', $data['hospital']);
        }

        if (isset($data['department'])) {
            $appointments->where('hospital_department_id', $data['department']);
        }

        return $appointments
            ->orderBy('starts_at', 'DESC')
            ->get();
    }

    public function get($id, $fail = false)
    {
        return $fail ? Appointment::findOrFail($id) : Appointment::find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'patient' => 'required|exists:patients,id',
            'hospital' => 'required|exists:hospitals,id',
            'department' => 'required|exists:hospital_departments,id',
            'starts_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|nullable|in:pending,confirmed,cancelled,success',
            'comments' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $appointment = new Appointment;
        $appointment->patient_id = $data['patient'];
        $appointment->hospital_id = $data['hospital'];
        $appointment->hospital_department_id = $data['department'];
        if (isset($data['status'])) {
            $appointment->status = $data['status'];
        }

        if (isset($data['starts_at'])) {
            $appointment->starts_at = $data['starts_at'];
        }

        if (isset($data['comments'])) {
            $appointment->comments = $data['comments'];
        }

        $appointment->save();
        $appointment->refresh();

        return $appointment;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'patient' => 'sometimes|nullable|exists:patients,id',
            'hospital' => 'sometimes|nullable|exists:hospitals,id',
            'department' => 'sometimes|nullable|exists:hospital_departments,id',
            'starts_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|nullable|in:pending,confirmed,cancelled,success',
            'comments' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $appointment = Appointment::findOrFail($id);
        if (isset($data['patient'])) {
            $appointment->patient_id = $data['patient'];
        }

        if (isset($data['hospital'])) {
            $appointment->hospital_id = $data['hospital'];
        }

        if (isset($data['department'])) {
            $appointment->hospital_department_id = $data['department'];
        }

        if (isset($data['status'])) {
            $appointment->status = $data['status'];
        }

        if (isset($data['starts_at'])) {
            $appointment->starts_at = $data['starts_at'];
        }

        if (isset($data['comments'])) {
            $appointment->comments = $data['comments'];
        }

        if ($appointment->isDirty()) {
            $appointment->save();
        }

        return $appointment;
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
    }
}
