<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginAdminTest extends SetupApp
{
    use RefreshDatabase;

    /**
     * Test success login
     *
     * @return void
     */
    public function testAdminLoginSeccess()
    {
        $this->getUser();
        $response = $this->postApi(route('admin.login'), [
            'email' => 'test@gamil.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [ 'token', 'expires_in', 'token_type', 'user' ]
        ]);
        $response->assertJson([
            'message' => 'Login Success'
        ]);
    }

    /**
     * test required login
     *
     * @return void
     */
    public function testAdminLoginValidation()
    {
        $this->getUser();
        $response = $this->postApi(route('admin.login'));
        $response->assertStatus(422);
        // dd($response->getData());
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [
                    'The email field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ]
            ]
        ]);
    }

     /**
     * test incorrect login
     *
     * @return void
     */
    public function testAdminLoginInCorrect()
    {
        $this->getUser();
        $response = $this->postApi(route('admin.login'), [
            'email' => 'test@gamil.com',
            'password' => 'passwordz'
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'status' => false,
            'message' => 'Email or password in correct',
            'data' => []
        ]);
    }

    public function testProfile()
    {
        $user = $this->getUser();
        $response = $this->withUser($user)->getApi(route('admin.profil'));
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Show Data',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' =>  $user->email
                ]
            ]
        ]);
    }

}
