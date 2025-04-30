<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_show_a_user()
  {
    $user = User::create([
      'name' => 'Test User',
      'email' => 'test@example.com',
      'phone' => '1234567890',
    ]);

    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'name',
          'email',
          'phone',
          'created_at',
          'updated_at'
        ]
      ]);
  }

  /** @test */
  public function it_returns_404_for_non_existent_user()
  {
    $response = $this->getJson('/api/users/999');

    $response->assertStatus(404)
      ->assertJson([
        'message' => 'User not found',
      ]);
  }
}
