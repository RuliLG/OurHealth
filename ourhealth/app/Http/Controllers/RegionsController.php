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

    /**
     * Gets a list of the available regions
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'regions' => $this->regionService->getAll()
        ]);
    }

    /**
     * Gets a list of the regions inside a country
     *
     * @param string $isoCode
     * @return \Illuminate\Http\Response
     */
    public function countryIndex($isoCode)
    {
        return response()->json([
            'regions' => $this->regionService->getAll($isoCode)
        ]);
    }

    /**
     * Gets a single region information
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        return response()->json($this->regionService->get($id, true));
    }
}
