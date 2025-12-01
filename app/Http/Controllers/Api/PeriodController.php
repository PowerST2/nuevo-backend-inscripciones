<?php

namespace App\Http\Controllers\Api;

use App\Traits\PeriodTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodController extends Controller
{
    use PeriodTrait;

    /**
     * Get all active periods
     */
    public function index()
    {
        $periods = $this->getPeriods();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Períodos obtenidos exitosamente',
            'data' => $periods,
            'count' => $periods->count(),
        ], Response::HTTP_OK);
    }

}
