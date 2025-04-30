<?php

namespace Tests\Feature\Addresses;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListUserAddressesTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_list_user_addresses()
  {
    $user = User::create([
      'name' => 'Test User',
      'email' => 'test@example.com',
      'phone' => '1234567890',
    ]);

    $user->addresses()->create([
      'street' => '123 Main St',
      'city' => 'New York',
      'state' => 'NY',
      'postal_code' => '10001',
      'country' => 'USA',
      'is_primary' => true,
    ]);

    $user->addresses()->create([
      'street' => '456 Oak Ave',
      'city' => 'Los Angeles',
      'state' => 'CA',
      'postal_code' => '90001',
      'country' => 'USA',
      'is_primary' => false,
    ]);

    $response = $this->getJson("/api/users/{$user->id}/addresses");

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          '*' => [
            'id',
            'user_id',
            'street',
            'city',
            'state',
            'postal_code',
            'country',
            'is_primary',
            'created_at',
            'updated_at'
          ]
        ]
      ]);

    $response->assertJsonCount(2, 'data');
  }

  /** @test */
  public function it_returns_404_for_non_existent_user()
  {
    $response = $this->getJson('/api/users/999/addresses');

    $response->assertStatus(404)
      ->assertJson([
        'message' => 'User not found',
      ]);
  }
}
