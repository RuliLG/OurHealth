<?php

namespace App\Http\Controllers;

use App\Http\Services\AppointmentService;
use App\Models\Hospital;
use App\Models\HospitalDepartment;
use App\Models\Patient;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class AppointmentsController extends Controller
{
    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'appointments' => $this->appointmentService->getAll($request->all())
        ]);
    }

    public function fromPatient(Patient $patient, Request $request)
    {
        $data = $request->all();
        $data['patient'] = $patient->id;
        return response()->json([
            'appointments' => $this->appointmentService->getAll($data)
        ]);
    }

    public function fromHospital(Hospital $hospital, Request $request)
    {
        $data = $request->all();
        $data['hospital'] = $hospital->id;
        unset($data['department']);
        return response()->json([
            'appointments' => $this->appointmentService->getAll($data)
        ]);
    }

    public function fromHospitalDepartment($hospitalId, $departmentId, Request $request)
    {
        $hospital = Hospital::findOrFail($hospitalId);
        $department = HospitalDepartment::findOrFail($departmentId);
        if ($department->hospital_id !== $hospital->id) {
            abort(422);
        }

        $data = $request->all();
        $data['hospital'] = $hospital->id;
        $data['department'] = $department->id;
        return response()->json([
            'appointments' => $this->appointmentService->getAll($data)
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
        try {
            return response()->json([
                'appointment' => $this->appointmentService->store($request->all())
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'appointment' => $this->appointmentService->get($id, true)
        ]);
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
        try {
            return response()->json([
                'appointment' => $this->appointmentService->update($id, $request->all())
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
        $this->appointmentService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
