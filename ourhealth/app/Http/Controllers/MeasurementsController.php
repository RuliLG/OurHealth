<?php

namespace App\Http\Controllers;

use App\Http\Services\MeasurementService;
use App\Models\Patient;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class MeasurementsController extends Controller
{
    public function __construct(MeasurementService $measurementService)
    {
        $this->measurementService = $measurementService;
    }

    /**
     * Display a list of the measurements from a specific patient
     *
     * @param Patient $patient
     * @return \Illuminate\Http\Response
     */
    public function index(Patient $patient)
    {
        return response()->json([
            'measurements' => $this->measurementService->getFromPatient($patient)
        ]);
    }

    /**
     * Display a list of the measurements frmo a specific patient and batchId
     *
     * @param Patient $patient
     * @param int $batchId
     * @return \Illuminate\Http\Response
     */
    public function indexFromBatchId(Patient $patient, $batchId)
    {
        return response()->json([
            'measurements' => $this->measurementService->getFromBatchId($patient, $batchId)
        ]);
    }

    /**
     * Store new measurements
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            return response()->json([
                'measurements' => $this->measurementService->store($request->all())
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unknown error' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified measurement
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->measurementService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
