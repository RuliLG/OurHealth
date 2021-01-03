<?php

namespace App\Http\Services;

use App\Models\ThirdPartyInsurance;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ThirdPartyInsuranceService
{
    /**
     * Returns a list of all the third party insurances available, ordered by name
     */
    public function getAll()
    {
        return ThirdPartyInsurance::orderBy('name', 'ASC')->get();
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:180|unique:third_party_insurances,name'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $thirdPartyInsurance = new ThirdPartyInsurance;
        $thirdPartyInsurance->name = $data['name'];
        $thirdPartyInsurance->save();

        return $thirdPartyInsurance;
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:180|unique:third_party_insurances,name,' . $id
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $thirdPartyInsurance = ThirdPartyInsurance::findOrFail($id);
        if (isset($data['name'])) {
            $thirdPartyInsurance->name = $data['name'];
        }

        if ($thirdPartyInsurance->isDirty()) {
            $thirdPartyInsurance->save();
        }

        return $thirdPartyInsurance;
    }

    public function destroy($id)
    {
        $thirdPartyInsurance = ThirdPartyInsurance::findOrFail($id);
        $thirdPartyInsurance->delete();
    }
}
