<?php

namespace App\Http\Controllers\Api;

use App\Traits\GenderTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenderController extends Controller
{
    use GenderTrait;

    /**
     * Get all active genders
     */
    public function index()
    {
        $genders = $this->getGenders();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Géneros obtenidos exitosamente',
            'data' => $genders,
        ], Response::HTTP_OK);
    }

}
