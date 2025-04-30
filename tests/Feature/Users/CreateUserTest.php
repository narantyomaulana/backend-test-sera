<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  /** @test */
  public function it_can_create_a_user()
  {
    $userData = [
      'name' => $this->faker->name,
      'email' => $this->faker->unique()->safeEmail,
      'phone' => $this->faker->phoneNumber,
    ];

    $response = $this->postJson('/api/users', $userData);

    $response->assertStatus(201)
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
      ]);

    $this->assertDatabaseHas('users', [
      'email' => $userData['email'],
    ]);
  }

  /** @test */
  public function it_requires_name_and_email()
  {
    $response = $this->postJson('/api/users', []);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['name', 'email']);
  }

  /** @test */
  public function email_must_be_unique()
  {
    $userData = [
      'name' => $this->faker->name,
      'email' => 'test@example.com',
      'phone' => $this->faker->phoneNumber,
    ];

    $this->postJson('/api/users', $userData);

    $response = $this->postJson('/api/users', $userData);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['email']);
  }
}
