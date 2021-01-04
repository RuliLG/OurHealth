<?php

namespace App\Http\Controllers;

use App\Http\Services\VisitService;
use App\Models\Allergy;
use App\Models\Condition;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Symptom;
use App\Models\User;
use App\Models\Visit;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class VisitsController extends Controller
{
    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }
    /**
     * Display a list of the visits from a hospital, patient or doctor
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'visits' => $this->visitService->getAll($request->all())
        ]);
    }

    /**
     * Display a list of the visits from a patient
     *
     * @param Patient $patient
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fromPatient(Patient $patient, Request $request)
    {
        $data = $request->all();
        $data['patient'] = $patient->id;
        return response()->json([
            'visits' => $this->visitService->getAll($data)
        ]);
    }

    /**
     * Display a list of the visits to a hospital
     *
     * @param Hospital $hospital
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fromHospital(Hospital $hospital, Request $request)
    {
        $data = $request->all();
        $data['hospital'] = $hospital->id;
        unset($data['doctor']);
        return response()->json([
            'visits' => $this->visitService->getAll($data)
        ]);
    }

    /**
     * Display the visits to a dictor
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fromDoctor(User $user, Request $request)
    {
        $data = $request->all();
        $data['doctor'] = $user->id;
        unset($data['hospital']);
        return response()->json([
            'visits' => $this->visitService->getAll($request->all())
        ]);
    }

    /**
     * Store a new visit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            return response()->json([
                'visit' => $this->visitService->store($request->all())
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
     * Display the specified visit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'visit' => $this->visitService->get($id, true)
        ]);
    }

    /**
     * Update the specified visit
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            return response()->json([
                'visit' => $this->visitService->update($id, $request->all())
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
     * Remove the specified visit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->visitService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Removes a condition from a visit
     *
     * @param Visit $visit
     * @param Condition $condition
     * @return \Illuminate\Http\Response
     */
    public function removeCondition(Visit $visit, Condition $condition)
    {
        $this->visitService->unlinkDiagnosis($visit, ['condition' => $condition->id]);
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Removes an allergy from a visit
     *
     * @param Visit $visit
     * @param Allergy $allergy
     * @return \Illuminate\Http\Response
     */
    public function removeAllergy(Visit $visit, Allergy $allergy)
    {
        $this->visitService->unlinkDiagnosis($visit, ['allergy' => $allergy->id]);
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Removes a symptom from a visit
     *
     * @param Visit $visit
     * @param Symptom $symptom
     * @return \Illuminate\Http\Response
     */
    public function removeSymptom(Visit $visit, Symptom $symptom)
    {
        $this->visitService->unlinkSymptom($visit, $symptom->id);
        return response()->json([
            'success' => true
        ]);
    }
}
