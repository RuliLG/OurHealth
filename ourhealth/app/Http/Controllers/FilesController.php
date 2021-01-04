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
     * Store a new file in both the database and the filesystem
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
     * Display the specified file.
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

    /**
     * Returns a list of the files belonging to a patient
     *
     * @param Patient $patient
     * @return \Illuminate\Http\Response
     */
    public function fromPatient(Patient $patient)
    {
        return response()->json([
            'files' => $this->fileService->getFromPatient($patient)
        ]);
    }

    /**
     * Returns a list of the files belonging to a visit
     *
     * @param Visit $visit
     * @return \Illuminate\Http\Response
     */
    public function fromVisit(Visit $visit)
    {
        return response()->json([
            'files' => $this->fileService->getFromVisit($visit)
        ]);
    }

    /**
     * Remove the specified file
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
