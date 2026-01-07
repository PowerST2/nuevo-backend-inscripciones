<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ScheduleActivityTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ScheduleActivityTrait;

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Verificar si la inscripción está abierta
        if (!$this->isRegistrationOpen()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Las inscripciones no están abiertas en este momento',
                'registration_status' => $this->getRegistrationStatus(),
            ], Response::HTTP_FORBIDDEN);
        }

        // Validar datos
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generar token
        $token = $user->createToken('auth-token')->plainTextToken;

        event(new Registered($user));

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token,
            'active_schedule' => $this->getActiveSchedule(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Login a user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales inválidas',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login exitoso',
            'user' => $user,
            'token' => $token,
            'registration_status' => $this->getRegistrationStatus(),
        ], Response::HTTP_OK);
    }

    /**
     * Logout a user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout exitoso',
        ], Response::HTTP_OK);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Perfil obtenido exitosamente',
            'user' => $request->user(),
        ], Response::HTTP_OK);
    }
}