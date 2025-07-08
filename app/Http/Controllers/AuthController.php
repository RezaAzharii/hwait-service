<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user (Saver)
     *
     * @bodyParam name string required Nama lengkap pengguna. Example: Budi Santoso
     * @bodyParam username string required Username unik. Only letters, numbers, underscores, dashes, and dots allowed. Example: budi_santoso
     * @bodyParam email string required Email pengguna. Example: budi@mail.com
     * @bodyParam password string required Minimal 6 karakter. Example: password123
     *
     * @response 201 {
     *   "status_code": 201,
     *   "message": "User berhasil terdaftar.",
     *   "user": {
     *     "id": 1,
     *     "username": "budi_santoso",
     *     "email": "budi@mail.com",
     *     "role": "saver"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'saver'
            ]);

            return response()->json([
                'status_code' => 201,
                'message' => 'User berhasil terdaftar.',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login pengguna
     *
     * @bodyParam email string required Email pengguna. Example: budi@mail.com
     * @bodyParam password string required Password pengguna. Example: password123
     *
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Login berhasil.",
     *   "data": {
     *     "id": 1,
     *     "username": "Budi Santoso",
     *     "email": "budi@mail.com",
     *     "role": "saver",
     *     "token": "eyJ0eXAiOiJKV1Qi..."
     *   }
     * }
     *
     * @response 401 {
     *   "status_code": 401,
     *   "message": "Email atau password salah.",
     *   "data": null
     * }
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Email atau password salah.',
                'data' => null
            ], 401);
        }

        try {
            $user = Auth::guard('api')->user();

            $formatedUser = [
                'id' => $user->id,
                'name' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'token' => $token
            ];

            return response()->json([
                'status_code' => 200,
                'message' => 'Login berhasil.',
                'data' => $formatedUser
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }

    /**
     * Ambil data user yang sedang login
     *
     * @authenticated
     *
     * @response 200 {
     *   "status_code": 200,
     *   "message": "User ditemukan",
     *   "data": {
     *     "id": 1,
     *     "name": "Budi Santoso",
     *     "username": "budi_santoso",
     *     "email": "budi@mail.com",
     *     "role": "saver"
     *   }
     * }
     */
    public function me()
    {
        try {
            $user = Auth::guard('api')->user();

            return response()->json([
                'status_code' => 200,
                'message' => 'User ditemukan',
                'data' => $user->makeHidden(['password', 'remember_token']),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Logout user
     *
     * Menghapus token JWT dan keluar dari sesi.
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Logout berhasil",
     *   "status_code": 200,
     *   "data": null
     * }
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logout berhasil',
            'status_code' => 200,
            'data' => null
        ]);
    }
}
