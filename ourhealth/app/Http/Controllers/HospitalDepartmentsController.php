<?php

namespace App\Http\Controllers;

use App\Http\Services\HospitalDepartmentService;
use App\Models\HospitalDepartment;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class HospitalDepartmentsController extends Controller
{
    public function __construct(HospitalDepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    /**
     * Display a list of the departments from a certain hospital
     *
     * @return \Illuminate\Http\Response
     */
    public function index($hospitalId)
    {
        try {
            return response()->json([
                'departments' => $this->departmentService->getAll(['hospital' => $hospitalId])
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
     * Store a new department inside a hospital
     *
     * @param int $hospitalId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($hospitalId, Request $request)
    {
        $data = $request->only([
            'name'
        ]);
        $data['hospital'] = $hospitalId;

        try {
            return response()->json([
                'department' => $this->departmentService->store($data)
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
     * Update the specified hospital department
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $hospitalId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $hospitalId, $id)
    {
        $department = HospitalDepartment::findOrFail($id);
        if ($department->hospital_id != $hospitalId) {
            abort(404);
        }

        $data = $request->only([
            'name'
        ]);

        try {
            return response()->json([
                'department' => $this->departmentService->update($id, $data)
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
     * Remove the specified hospital department
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($hospitalId, $id)
    {
        $department = HospitalDepartment::findOrFail($id);
        if ($department->hospital_id != $hospitalId) {
            abort(404);
        }

        $this->departmentService->destroy($id, true);
        return response()->json(['success' => true]);
    }
}
