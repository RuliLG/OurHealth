<?php

namespace App\Http\Services;

use App\Models\HospitalDepartment;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class HospitalDepartmentService
{
    public function getAll($options = [])
    {
        $validator = Validator::make($options, [
            'hospital' => 'required|exists:hospitals,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        return HospitalDepartment::orderBy('name', 'ASC')->get();
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'hospital' => 'required|exists:hospitals,id',
            'name' => 'required|string|max:40',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $department = new HospitalDepartment;
        $department->hospital_id = intval($data['hospital']);
        $department->name = $data['name'];
        $department->save();
        return $department;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:40',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $department = HospitalDepartment::findOrFail($id);
        if (isset($data['name'])) {
            $department->name = $data['name'];
        }

        if ($department->isDirty()) {
            $department->save();
        }

        return $department;
    }

    public function destroy($id, $fail = false)
    {
        if ($fail) {
            $hospital = HospitalDepartment::findOrFail($id);
            $hospital->delete();
        } else {
            HospitalDepartment::destroy($id);
        }
    }
}
