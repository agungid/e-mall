<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SetupApp extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function getUser()
    {
        return User::create([
            'name' => 'User Test',
            'email' => 'test@gamil.com',
            'password' => Hash::make('password')
        ]);
    }

    public function withHeaders($headers)
    {
        foreach ($headers as $key => $val) {
            $this->withHeader($key, $val);
        }
        return $this;
    }

    public function postApi($url, array $data = [], array $headers = [])
    {
        $headers = array_merge([
            'Accept' => 'application/json'
        ], $headers);
        $this->withHeaders($headers);

        return $this->json('POST', $url, $data, $headers);
    }

    public function getApi($url, array $headers = [])
    {
        $headers = array_merge([
            'Accept' => 'application/json'
        ], $headers);
        $this->withHeaders($headers);
        return $this->json('GET', $url);
    }

    public function getToken()
    {
        $this->getUser();
        $authenticate = $this->postApi('/api/v1/login', [
            'email' => 'test@gamil.com',
            'password' => 'password'
        ]);
        
        return $authenticate->data->token;
    }

    public function withTokens($token)
    {
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $this;
    }

    public function withUser(Authenticatable $user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withTokens($token);
    }


}
