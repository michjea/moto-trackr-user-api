<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUserRegistration()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
            'device_name' => 'Test Device',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }

    public function testUserLogin()
    {
        $user = User::factory(User::class)->create(['password' => Hash::make('testpassword')]);

        $data = [
            'email' => $user->email,
            'password' => 'testpassword',
            'device_name' => 'Test Device',
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }
}
