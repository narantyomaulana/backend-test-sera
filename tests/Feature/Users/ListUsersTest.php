<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListUsersTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_list_users()
  {
    User::create([
      'name' => 'User 1',
      'email' => 'user1@example.com',
      'phone' => '1234567890',
    ]);

    User::create([
      'name' => 'User 2',
      'email' => 'user2@example.com',
      'phone' => '0987654321',
    ]);

    $response = $this->getJson('/api/users');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          '*' => [
            'id',
            'name',
            'email',
            'phone',
            'created_at',
            'updated_at'
          ]
        ],
        'meta' => [
          'current_page',
          'last_page',
          'per_page',
          'total'
        ]
      ]);

    $response->assertJsonCount(2, 'data');
  }

  /** @test */
  public function it_paginates_users()
  {
    for ($i = 1; $i <= 20; $i++) {
      User::create([
        'name' => "User $i",
        'email' => "user$i@example.com",
        'phone' => "123456789$i",
      ]);
    }

    $response = $this->getJson('/api/users?per_page=10');

    $response->assertStatus(200)
      ->assertJsonPath('meta.per_page', 10)
      ->assertJsonPath('meta.current_page', 1)
      ->assertJsonPath('meta.last_page', 2)
      ->assertJsonPath('meta.total', 20)
      ->assertJsonCount(10, 'data');

    $response = $this->getJson('/api/users?page=2&per_page=10');

    $response->assertStatus(200)
      ->assertJsonPath('meta.current_page', 2)
      ->assertJsonCount(10, 'data');
  }
}
