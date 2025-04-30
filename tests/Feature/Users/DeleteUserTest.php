<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_delete_a_user()
  {
    $user = User::create([
      'name' => 'Test User',
      'email' => 'test@example.com',
      'phone' => '1234567890',
    ]);

    $response = $this->deleteJson("/api/users/{$user->id}");

    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User deleted successfully',
      ]);

    $this->assertDatabaseMissing('users', [
      'id' => $user->id,
    ]);
  }

  /** @test */
  public function it_returns_404_for_non_existent_user_deletion()
  {
    $response = $this->deleteJson('/api/users/999');

    $response->assertStatus(404)
      ->assertJson([
        'message' => 'User not found',
      ]);
  }

  /** @test */
  public function it_deletes_addresses_when_user_is_deleted()
  {
    $user = User::create([
      'name' => 'Test User',
      'email' => 'test@example.com',
      'phone' => '1234567890',
    ]);

    $user->addresses()->create([
      'street' => '123 Test St',
      'city' => 'Test City',
      'state' => 'TS',
      'postal_code' => '12345',
      'country' => 'Test Country',
      'is_primary' => true,
    ]);

    $this->deleteJson("/api/users/{$user->id}");

    $this->assertDatabaseMissing('addresses', [
      'user_id' => $user->id,
    ]);
  }
}
