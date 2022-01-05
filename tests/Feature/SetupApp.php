<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SetupApp extends TestCase
{
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


}
