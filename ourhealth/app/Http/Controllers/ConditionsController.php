<?php

namespace App\Http\Controllers;

use App\Http\Services\ConditionService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ConditionsController extends Controller
{
    public function __construct(ConditionService $conditionService)
    {
        $this->conditionService = $conditionService;
    }
    /**
     * Display a list of the available medical conditions
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'conditions' => $this->conditionService->getAll()
        ]);
    }

    /**
     * Store a new medical condition in the database
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
                'condition' => $this->conditionService->store($data)
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
     * Display the specified medical condition.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'condition' => $this->conditionService->get($id, true)
        ]);
    }

    /**
     * Update the specified medical condition
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
                'condition' => $this->conditionService->update($id, $data)
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
     * Remove the specified medical condition
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->conditionService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
