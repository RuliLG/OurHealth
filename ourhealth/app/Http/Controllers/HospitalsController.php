<?php

namespace App\Http\Controllers;

use App\Http\Services\HospitalService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class HospitalsController extends Controller
{
    public function __construct(HospitalService $hospitalService)
    {
        $this->hospitalService = $hospitalService;
    }
    /**
     * Display a list of the available hospitals, filtered by country or region
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $request->only([
            'country',
            'region'
        ]);

        try {
            return response()->json([
                'hospitals' => $this->hospitalService->getAll($data)
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
     * Store a new hospital inside a region
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'region',
            'name'
        ]);

        try {
            return response()->json([
                'hospital' => $this->hospitalService->store($data)
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
     * Display the specified hospital.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->hospitalService->get($id, true));
    }

    /**
     * Update the specified hospital
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'region',
            'name'
        ]);

        try {
            return response()->json([
                'hospital' => $this->hospitalService->update($id, $data)
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
     * Remove the specified hospital
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->hospitalService->destroy($id, true);
        return response()->json(['success' => true]);
    }
}
