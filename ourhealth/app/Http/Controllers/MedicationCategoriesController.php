<?php

namespace App\Http\Controllers;

use App\Http\Services\MedicationCategoryService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class MedicationCategoriesController extends Controller
{
    public function __construct(MedicationCategoryService $medicationCategoryService)
    {
        $this->medicationCategoryService = $medicationCategoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'categories' => $this->medicationCategoryService->getAll()
        ]);
    }

    public function full()
    {
        return response()->json([
            'categories' => $this->medicationCategoryService->getAllWithMedications()
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
            'name',
        ]);

        try {
            return response()->json([
                'category' => $this->medicationCategoryService->store($data)
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
            'category' => $this->medicationCategoryService->get($id, true)
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
        $data = $request->only([
            'name',
        ]);

        try {
            return response()->json([
                'category' => $this->medicationCategoryService->update($id, $data)
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
        $this->medicationCategoryService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
