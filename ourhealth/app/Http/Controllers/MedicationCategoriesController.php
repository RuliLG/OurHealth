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
     * Display a list of the available categories
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'categories' => $this->medicationCategoryService->getAll()
        ]);
    }

    /**
     * Display a list of the available categories with medication information filled
     *
     * @return \Illuminate\Http\Response
     */
    public function full()
    {
        return response()->json([
            'categories' => $this->medicationCategoryService->getAllWithMedications()
        ]);
    }

    /**
     * Store a new medication category
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
     * Display the specified category
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
     * Update the specified category
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
     * Remove the specified category
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
