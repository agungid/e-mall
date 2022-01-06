<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if(!$token = auth()->guard('api_customer')->attempt($credentials)) {
            return ResponseService::toJson(false, 'Email or Password is incorrect', 401);

        }
        
        return ResponseService::toJson(true, 'Login Success', 200, [
            'token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'token_type' => 'Bearer',
            'user' => auth()->guard('api_admin')->user()
        ]);
    }

    /**
     * show profile admin
     *
     * @return void
     */
    public function getUser()
    {
        $user = auth()->guard('api_customer')->user();
        return ResponseService::toJson(true, 'Show Data', 200, [ 'user' => $user]);
    }
    
    /**
     * logout customer
     *
     * @return void
     */
    public function logout()
    {
        if(JWTAuth::invalidate(JWTAuth::getToken())) {
            return ResponseService::toJson(true,'Logout success',200);
        }
    }

     /**
     * refreshToken
     *
     * @param  mixed $request
     * @return void
     */
    public function refreshToken(Request $request)
    {
        //refresh "token"
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        //set user dengan "token" baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set header "Authorization" dengan type Bearer + "token" baru
        $request->headers->set('Authorization','Bearer '.$refreshToken);

        return ResponseService::toJson(true, 'Refresh token success', 200, [
            'token' => $refreshToken,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
