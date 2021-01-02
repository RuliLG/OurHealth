<?php

namespace App\Http\Controllers;

use App\Http\Services\ThirdPartyInsuranceService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ThirdPartyInsurancesController extends Controller
{
    public function __construct(ThirdPartyInsuranceService $thirdPartyInsuranceService)
    {
        $this->thirdPartyInsuranceService = $thirdPartyInsuranceService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'insurances' => $this->thirdPartyInsuranceService->getAll()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'name'
        ]);

        try {
            return response()->json([
                'insurance' => $this->thirdPartyInsuranceService->store($data)
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unknown error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name'
        ]);

        try {
            return response()->json([
                'insurance' => $this->thirdPartyInsuranceService->update($id, $data)
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unknown error'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->thirdPartyInsuranceService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
