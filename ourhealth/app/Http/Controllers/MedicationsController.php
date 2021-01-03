<?php

namespace App\Http\Controllers;

use App\Http\Services\MedicationService;
use App\Models\Allergy;
use App\Models\Condition;
use App\Models\Medication;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class MedicationsController extends Controller
{
    public function __construct(MedicationService $medicationService)
    {
        $this->medicationService = $medicationService;
    }

    public function index()
    {
        return response()->json([
            'medications' => $this->medicationService->getAll()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'medication' => $this->medicationService->get($id, true)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'commercial_name',
            'active_ingredient',
            'kind',
            'category',
            'allergies',
            'conditions',
            'incompatibilities',
        ]);

        try {
            return response()->json([
                'medication' => $this->medicationService->store($data)
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

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'commercial_name',
            'active_ingredient',
            'kind',
            'category',
            'allergies',
            'conditions',
            'incompatibilities',
        ]);

        try {
            return response()->json([
                'medication' => $this->medicationService->update($id, $data)
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

    public function destroy($id)
    {
        $this->medicationService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }

    public function storeCondition(Medication $medication, Condition $condition)
    {
        $this->medicationService->linkCondition($medication, $condition->id);
        return response()->json([
            'medication' => $this->medicationService->get($medication->id)
        ]);
    }

    public function destroyCondition(Medication $medication, Condition $condition)
    {
        $this->medicationService->unlinkCondition($medication, $condition->id);
        return response()->json([
            'medication' => $this->medicationService->get($medication->id)
        ]);
    }

    public function storeAllergy(Medication $medication, Allergy $allergy)
    {
        $this->medicationService->linkAllergy($medication, $allergy->id);
        return response()->json([
            'medication' => $this->medicationService->get($medication->id)
        ]);
    }

    public function destroyAllergy(Medication $medication, Allergy $allergy)
    {
        $this->medicationService->unlinkAllergy($medication, $allergy->id);
        return response()->json([
            'medication' => $this->medicationService->get($medication->id)
        ]);
    }

    public function storeIncompatibility(Medication $medication, Medication $incompatibleWith)
    {
        $this->medicationService->linkIncompatibility($medication, $incompatibleWith);
        return response()->json([
            'medication' => $this->medicationService->get($medication->id)
        ]);
    }

    public function destroyIncompatibility(Medication $medication, Medication $incompatibleWith)
    {
        $this->medicationService->unlinkIncompatibility($medication, $incompatibleWith);
        return response()->json([
            'medication' => $this->medicationService->get($medication->id)
        ]);
    }
}
