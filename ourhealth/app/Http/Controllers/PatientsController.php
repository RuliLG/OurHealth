<?php

namespace App\Http\Controllers;

use App\Http\Services\PatientService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PatientsController extends Controller
{
    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }
    /**
     * Display a list of the patients
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'patients' => $this->patientService->getAll()
        ]);
    }

    /**
     * Store a new patient
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            return response()->json([
                'patient' => $this->patientService->store($request->all())
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified patient
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'patient' => $this->patientService->get($id, true)
        ]);
    }

    /**
     * Display the specified patient by providing their isoCode and national ID
     *
     * @param string $isoCode
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function showByCountryId($isoCode, $id)
    {
        return response()->json([
            'patient' => $this->patientService->getByCountryId($isoCode, $id, true)
        ]);
    }

    /**
     * Update the specified patient
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            return response()->json([
                'patient' => $this->patientService->update($id, $request->all())
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified patient
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->patientService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
