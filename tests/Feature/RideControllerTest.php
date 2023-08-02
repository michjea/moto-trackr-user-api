<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ride;
use App\Models\User;

class RideControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCreateRide()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $token = $user->createToken('test-token')->plainTextToken;

        $data = [
            'title' => 'Test Ride',
            'description' => 'A test ride',
            'public' => true,
            'distance' => 10.5,
            'duration' => 60,
            'max_speed' => 30.5,
            'avg_speed' => 20.5,
            'positions' => [0], // Add test positions here
            'route' => [0], // Add test route here
        ];

        $response = $this->postJson('/api/ride', $data, ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Ride created']);
    }

    public function testShowRide()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $ride = Ride::factory()->create(['public' => true, 'user_id' => $user->id]);

        $response = $this->getJson('/api/ride/' . $ride->id, ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200)
            ->assertJson(['id' => $ride->id]);
    }
}
