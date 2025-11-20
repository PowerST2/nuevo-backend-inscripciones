<?php

namespace App\Http\Controllers\Api;

use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodController extends Controller
{
    /**
     * Get all periods
     */
    public function index()
    {
        $periods = Period::all();

        return response()->json([
            'message' => 'Períodos obtenidos exitosamente',
            'data' => $periods,
            'count' => $periods->count(),
        ], Response::HTTP_OK);
    }

    /**
     * Get a specific period
     */
    public function show($id)
    {
        $period = Period::find($id);

        if (!$period) {
            return response()->json([
                'message' => 'Período no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Período obtenido exitosamente',
            'data' => $period,
        ], Response::HTTP_OK);
    }

    /**
     * Create a new period
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:periods',
            'name' => 'required|string',
            'active' => 'boolean',
        ]);

        $period = Period::create($validated);

        return response()->json([
            'message' => 'Período creado exitosamente',
            'data' => $period,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a period
     */
    public function update(Request $request, $id)
    {
        $period = Period::find($id);

        if (!$period) {
            return response()->json([
                'message' => 'Período no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'code' => 'string|unique:periods,code,' . $id,
            'name' => 'string',
            'active' => 'boolean',
        ]);

        $period->update($validated);

        return response()->json([
            'message' => 'Período actualizado exitosamente',
            'data' => $period,
        ], Response::HTTP_OK);
    }

    /**
     * Delete a period
     */
    public function destroy($id)
    {
        $period = Period::find($id);

        if (!$period) {
            return response()->json([
                'message' => 'Período no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        $period->delete();

        return response()->json([
            'message' => 'Período eliminado exitosamente',
        ], Response::HTTP_OK);
    }
}
