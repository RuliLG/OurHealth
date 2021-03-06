<?php

namespace App\Http\Services;

use App\Models\Hospital;
use App\Models\HospitalDepartment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class UserService
{
    public function login($email, $password)
    {
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            // Refresh the user token each time they log in
            $user->api_token = Str::random(120);
            $user->save();
            return $user;
        }

        return null;
    }

    public function get($id)
    {
        $user = User::with('hospital', 'hospital_department')->findOrFail($id);
        Gate::authorize('show', $user);
        return $user;
    }

    public function store($data)
    {
        Gate::authorize('create-user');
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'email' => 'required|string|max:180|email:rfc,dns|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'profile_picture' => 'sometimes|nullable|file|image|dimensions:min_width=150,min_height=150',
            'hospital' => 'required|exists:hospitals,id',
            'hospital_department' => 'required|exists:hospital_departments,id',
            'position' => 'sometimes|nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $hospital = auth()->user()->is_hospital_admin ? auth()->user()->hospital : Hospital::findOrFail($data['hospital']);
        $hospitalDepartment = HospitalDepartment::findOrFail($data['hospital_department']);
        if ($hospitalDepartment->hospital_id !== $hospital->id) {
            throw new InvalidParameterException('Invalid hospital department');
        }

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_active = true;
        $user->password = Hash::make($data['password']);
        $user->hospital_id = intval($data['hospital']);
        $user->hospital_department_id = intval($data['hospital_department']);
        $user->api_token = Str::random(120);
        if (isset($data['last_name'])) {
            $user->last_name = $data['last_name'];
        }

        if (isset($data['position'])) {
            $user->position = $data['position'];
        }

        if (isset($data['profile_picture'])) {
            $s3Path = (new FileService)->upload($data['profile_picture'], 'avatars');
            $user->profile_picture_s3_key = $s3Path;
        }

        $user->save();

        return $user;
    }

    public function update($id, $data)
    {
        $user = User::findOrFail($id);
        Gate::authorize('update', $user);
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|string|max:180|email:rfc,dns|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8|max:255',
            'profile_picture' => 'sometimes|nullable|file|image|dimensions:min_width=150,min_height=150',
            'hospital' => 'sometimes|nullable|exists:hospitals,id',
            'hospital_department' => 'sometimes|nullable|exists:hospital_departments,id',
            'position' => 'sometimes|nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        if (isset($data['hospital'])) {
            $hospital = auth()->user()->is_hospital_admin ? auth()->user()->hospital : Hospital::findOrFail($data['hospital']);
        } else {
            $hospital = $user->hospital;
        }

        if (isset($data['hospital_department'])) {
            $hospitalDepartment = HospitalDepartment::findOrFail($data['hospital_department']);
            if ($hospitalDepartment->hospital_id !== $hospital->id) {
                throw new InvalidParameterException('Invalid hospital department');
            }
        }

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['is_active'])) {
            $user->is_active = true;
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (isset($data['hospital'])) {
            $user->hospital_id = intval($data['hospital']);
        }

        if (isset($data['hospital_department'])) {
            $user->hospital_department_id = intval($data['hospital_department']);
        }

        if (isset($data['last_name'])) {
            $user->last_name = $data['last_name'];
        }

        if (isset($data['position'])) {
            $user->position = $data['position'];
        }

        if (isset($data['profile_picture'])) {
            $s3Path = (new FileService)->upload($data['profile_picture'], 'avatars');
            $user->profile_picture_s3_key = $s3Path;
        }

        if ($user->isDirty()) {
            $user->save();
        }

        return $user;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('delete', $user);
        $user->delete();
    }
}
