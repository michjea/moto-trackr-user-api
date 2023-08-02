<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ride;
use App\Models\User;

class FeedControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testFetchFeedForAuthenticatedUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some test rides with public=true
        Ride::factory(5)->create(['public' => true]);

        // Create some test rides with public=false
        Ride::factory(3)->create(['public' => false]);

        $response = $this->getJson('/api/feed');

        $response->assertStatus(200)
            ->assertJsonCount(5); // Assert that only public rides are fetched
    }
}
