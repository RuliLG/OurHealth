<?php

namespace App\Http\Services;

use App\Models\HospitalDepartment;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class HospitalDepartmentService
{
    /**
     * Returns a list of all the hospitals ordered by name
     *
     * @return array<HospitalDepartment>
     */
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

    /**
     * Creates a hospital department
     *
     * @return HospitalDepartment
     */
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

    /**
     * Updates a hospital department
     *
     * @return HospitalDepartment
     */
    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:40',
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

    /**
     * Removes a hospital department from its id
     *
     * @param string $id
     * @param boolean $fail if the function should abort with a 404 code if the hospital is not found
     * @return void
     */
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
