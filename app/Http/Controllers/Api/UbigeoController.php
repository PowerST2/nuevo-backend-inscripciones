<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ubigeo;
use App\Traits\UbigeoTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UbigeoController extends Controller
{
    use UbigeoTrait;

    /**
     * 1. GET /api/ubigeos/departments
     */
    public function departments()
    {
        $departments = $this->getDepartments();

        return response()->json([
            'status' => 'success',
            'data' => $departments,
        ], Response::HTTP_OK);
    }

    /**
     * 2. GET /api/ubigeos/provinces?department_code=150000
     */
    public function provinces(Request $request)
    {
        $request->validate([
            'department_code' => 'required|string|size:6'
        ]);

        $provinces = $this->getProvincesByDepartmentCode($request->query('department_code'));

        return response()->json([
            'status' => 'success',
            'data' => $provinces,
        ], Response::HTTP_OK);
    }

    /**
     * 3. GET /api/ubigeos/districts?province_code=150100
     */
    public function districts(Request $request)
    {
        $request->validate([
            'province_code' => 'required|string|size:6',
        ]);

        $districts = $this->getDistrictsByProvinceCode($request->query('province_code'));

        return response()->json([
            'status' => 'success',
            'data' => $districts,
        ], Response::HTTP_OK);
    }

    /**
     * GET /api/ubigeos/{id}
     */
    public function show(int $id)
    {
        $ubigeo = $this->getUbigeoById($id);

        if (!$ubigeo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubigeo no encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'data' => $ubigeo,
        ], Response::HTTP_OK);
    }
}