<?php

namespace App\Http\Controllers;

use App\Http\Services\ReportService;
use App\Models\File;
use App\Models\Measurement;
use App\Models\Patient;
use App\Models\Report;
use App\Models\Visit;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ReportsController extends Controller
{
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'reports' => $this->reportService->getAll($request->all())
        ]);
    }

    public function fromPatient(Patient $patient, Request $request)
    {
        $data = $request->all();
        $data['patient'] = $patient->id;
        return response()->json([
            'reports' => $this->reportService->getAll($data)
        ]);
    }

    public function fromVisit(Visit $visit, Request $request)
    {
        $data = $request->all();
        $data['patient'] = $visit->patient_id;
        $data['visit'] = $visit->id;
        return response()->json([
            'reports' => $this->reportService->getAll($data)
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
                'report' => $this->reportService->store($request->all())
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'report' => $this->reportService->get($id, true)
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
                'report' => $this->reportService->update($id, $request->all())
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->reportService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }

    public function unlinkMeasurement(Report $report, Measurement $measurement)
    {
        $this->reportService->unlinkMeasurement($report, $measurement->id);
        return response()->json([
            'success' => true
        ]);
    }

    public function unlinkFile(Report $report, File $file)
    {
        $this->reportService->unlinkFile($report, $file->id);
        return response()->json([
            'success' => true
        ]);
    }
}
