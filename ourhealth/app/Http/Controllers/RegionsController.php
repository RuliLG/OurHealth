<?php

namespace App\Http\Controllers;

use App\Http\Services\RegionService;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    public function index()
    {
        return response()->json([
            'regions' => $this->regionService->getAll()
        ]);
    }

    public function countryIndex($isoCode)
    {
        return response()->json([
            'regions' => $this->regionService->getAll($isoCode)
        ]);
    }

    public function get($id)
    {
        return response()->json($this->regionService->get($id, true));
    }
}
