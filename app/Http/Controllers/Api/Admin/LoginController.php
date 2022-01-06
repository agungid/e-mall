<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\ResponseService;

class LoginController extends Controller
{    
    /**
     * login admin
     *
     * @param  mixed $request
     * @return void
     */
    public function index(LoginRequest $request)
    {
        $creadential = $request->only("email", "password");

        if(!$token = auth()->guard("api_admin")->attempt($creadential)) {
            return ResponseService::toJson(false,"Email or password in correct", 401);
        }

        return ResponseService::toJson(true, "Login Success", 200, [
            "token" => $token,
            "token_type" => "Bearer",
            "user" => auth()->guard("api_admin")->user()
        ]);
    }
    
    /**
     * show profile admin
     *
     * @return void
     */
    public function getUser()
    {
        $user = auth()->guard("api_admin")->user();
        return ResponseService::toJson(true, "Show Data", 200, [ "user" => $user]);
    }
}
