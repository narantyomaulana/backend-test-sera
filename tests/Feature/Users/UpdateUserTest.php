<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  /** @test */
  public function it_can_update_a_user()
  {
    $user = User::create([
      'name' => 'Original Name',
      'email' => 'original@example.com',
      'phone' => '1234567890',
    ]);

    $updatedData = [
      'name' => 'Updated Name',
      'email' => 'updated@example.com',
      'phone' => '0987654321',
    ];

    $response = $this->putJson("/api/users/{$user->id}", $updatedData);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'message',
        'data' => [
          'id',
          'name',
          'email',
          'phone',
          'created_at',
          'updated_at'
        ]
      ])
      ->assertJsonPath('data.name', 'Updated Name')
      ->assertJsonPath('data.email', 'updated@example.com')
      ->assertJsonPath('data.phone', '0987654321');

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'name' => 'Updated Name',
      'email' => 'updated@example.com',
      'phone' => '0987654321',
    ]);
  }

  /** @test */
  public function it_validates_email_uniqueness_on_update()
  {
    $user1 = User::create([
      'name' => 'User One',
      'email' => 'user1@example.com',
      'phone' => '1111111111',
    ]);

    $user2 = User::create([
      'name' => 'User Two',
      'email' => 'user2@example.com',
      'phone' => '2222222222',
    ]);

    $response = $this->putJson("/api/users/{$user2->id}", [
      'email' => 'user1@example.com',
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['email']);
  }

  /** @test */
  public function it_returns_404_for_non_existent_user_update()
  {
    $response = $this->putJson('/api/users/999', [
      'name' => 'Updated Name',
    ]);

    $response->assertStatus(404)
      ->assertJson([
        'message' => 'User not found',
      ]);
  }
}
