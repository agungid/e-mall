<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function index(LoginRequest $request)
    {
        $creadential = $request->only('email', 'password');

        if(!$token = auth()->guard('api_admin')->attempt($creadential)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email or password in correct',
                'data'    => []
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message'=> 'Login success',
            'data'   => [
                'token' => $token,
                'user' => auth()->guard('api_admin')->user()
            ],
        ]);
    }
}
