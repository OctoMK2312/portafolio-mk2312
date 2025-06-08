<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^(?:0?1?2?3?4?5?6?7?8?9?)+$/', $value)) {
                        $fail('La contraseña no puede ser una secuencia numérica consecutiva.');
                    }
                    if (preg_match('/^(?:9?8?7?6?5?4?3?2?1?0?)+$/', $value)) {
                        $fail('La contraseña no puede ser una secuencia numérica consecutiva.');
                    }
                    if (!preg_match('/[A-Z]/', $value) || !preg_match('/[a-z]/', $value) || !preg_match('/[0-9]/', $value)) {
                        $fail('La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número.');
                    }
                },
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'username.unique' => 'El nombre de usuario ya ha sido tomado.',
            'email.unique' => 'El correo electrónico ya ha sido registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'role' => User::ROLE_USER,
            'bio' => $request->bio ?? null,
            'profile_picture' => $request->profile_picture ?? 'avatars/default.png',
            'active' => true,
        ]);
        return response()->json([
            'message' => 'Usuario registrado exitosamente.',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo válida.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
        ]);

        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
