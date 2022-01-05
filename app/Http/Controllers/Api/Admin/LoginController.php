<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\ResponseService;

class LoginController extends Controller
{
    public function index(LoginRequest $request)
    {
        $creadential = $request->only('email', 'password');

        if(!$token = auth()->guard('api_admin')->attempt($creadential)) {
            return ResponseService::toJson(false,'Email or password in correct', 401);
        }

        return ResponseService::toJson(true, 'Login Success', 200, [
            'token' => $token,
            'user' => auth()->guard('api_admin')->user()
        ]);
    }
}
