<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Método para iniciar sesión
    public function login(LoginRequest $request)
    {
        // Obtener las credenciales validadas
        $credentials = $request->validated();

        // Verificar si las credenciales son correctas
        $user = User::where('email', $credentials['email'])->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Si la autenticación es exitosa, generar un token de Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // Crear el recurso del usuario
            $result = new UserResource($user);

            // Retornar la respuesta con el token y los detalles del usuario
            return response()->json([
                'token' => $token,
                'user' => $result,
            ], 200);
        }

        // Si las credenciales no son correctas
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Método para registrar un nuevo usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token], 201);
    }

    // Método para cerrar sesión
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // Método para obtener información del usuario autenticado
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}