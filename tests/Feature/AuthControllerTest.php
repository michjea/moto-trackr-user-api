<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{

    public function test_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john.doe13@exemple.com',
            'password' => 'thisisasupersecurepassword',
            'password_confirmation' => 'thisisasupersecurepassword',
            'device_name' => 'test'
        ]);

        $response->assertStatus(201);
    }
}
