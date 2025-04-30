<?php

namespace Tests\Feature\Addresses;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowUserAddressTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_show_a_user_address()
  {
    $user = User::create([
      'name' => 'Test User',
      'email' => 'test@example.com',
      'phone' => '1234567890',
    ]);

    $address = $user->addresses()->create([
      'street' => '123 Main St',
      'city' => 'New York',
      'state' => 'NY',
      'postal_code' => '10001',
      'country' => 'USA',
      'is_primary' => true,
    ]);

    $response = $this->getJson("/api/users/{$user->id}/addresses/{$address->id}");

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
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
      ])
      ->assertJsonPath('data.street', '123 Main St')
      ->assertJsonPath('data.city', 'New York');
  }

  /** @test */
  public function it_returns_404_for_non_existent_user()
  {
    $response = $this->getJson('/api/users/999/addresses/1');

    $response->assertStatus(404)
      ->assertJson([
        'message' => 'User not found',
      ]);
  }

  /** @test */
  public function it_returns_404_for_non_existent_address()
  {
    $user = User::create([
      'name' => 'Test User',
      'email' => 'test@example.com',
      'phone' => '1234567890',
    ]);

    $response = $this->getJson("/api/users/{$user->id}/addresses/999");

    $response->assertStatus(404)
      ->assertJson([
        'message' => 'Address not found for this user',
      ]);
  }
}
