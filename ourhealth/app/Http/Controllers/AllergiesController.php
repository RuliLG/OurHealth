<?php

namespace App\Http\Controllers;

use App\Http\Services\AllergyService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class AllergiesController extends Controller
{
    public function __construct(AllergyService $allergyService)
    {
        $this->allergyService = $allergyService;
    }
    /**
     * Display a list of all the allergies
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'allergies' => $this->allergyService->getAll()
        ]);
    }

    /**
     * Store a new allergy in the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'name',
            'severity',
            'description'
        ]);

        try {
            return response()->json([
                'allergy' => $this->allergyService->store($data)
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
     * Display an allergy
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'allergy' => $this->allergyService->get($id, true)
        ]);
    }

    /**
     * Update an allergy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name',
            'severity',
            'description'
        ]);

        try {
            return response()->json([
                'allergy' => $this->allergyService->update($id, $data)
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
        $this->allergyService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
