<?php

namespace App\Http\Services;

use App\Models\Condition;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ConditionService
{
    public function getAll()
    {
        return Condition::orderBy('name', 'ASC')->get();
    }

    public function get($id, $fail = false)
    {
        return $fail ? Condition::findOrFail($id) : Condition::find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:180|unique:conditions,name',
            'severity' => 'required|string|in:none,low,mid,high',
            'description' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $condition = new Condition;
        $condition->name = $data['name'];
        $condition->severity = $data['severity'];
        if (isset($data['description'])) {
            $condition->description_html = $data['description'];
        }

        $condition->save();

        return $condition;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:180|unique:conditions,name,' . $id,
            'severity' => 'sometimes|nullable|string|in:none,low,mid,high',
            'description' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $condition = Condition::findOrFail($id);
        if (isset($data['name'])) {
            $condition->name = $data['name'];
        }

        if (isset($data['severity'])) {
            $condition->severity = $data['severity'];
        }

        if (isset($data['description'])) {
            $condition->description_html = $data['description'];
        }

        if ($condition->isDirty()) {
            $condition->save();
        }

        return $condition;
    }

    public function destroy($id)
    {
        $condition = Condition::findOrFail($id);
        $condition->delete();
    }
}
