<?php

namespace App\Http\Controllers;

use App\Http\Services\FileService;
use App\Models\Patient;
use App\Models\Visit;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class FilesController extends Controller
{
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
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
                'file' => $this->fileService->store($request->all())
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'file' => $this->fileService->get($id, true)
        ]);
    }

    public function fromPatient(Patient $patient)
    {
        return response()->json([
            'files' => $this->fileService->getFromPatient($patient)
        ]);
    }

    public function fromVisit(Visit $visit)
    {
        return response()->json([
            'files' => $this->fileService->getFromVisit($visit)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->fileService->destroy($id);
            return response()->json([
                'success' => true
            ]);
        } catch (AccessDeniedHttpException $e) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }
    }
}
