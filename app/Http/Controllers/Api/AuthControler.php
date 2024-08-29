<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthControler extends Controller
{
    public function login(Request $request)
    {
        //
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user || ! Hash::check( $request->password, $user->password)){
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Password dan Email Salah',
            ], 422);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ],
            'message' => 'Login Berhasil',
        ], 200);

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
