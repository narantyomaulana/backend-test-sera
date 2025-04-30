<?php

namespace Tests\Feature\Addresses;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserAddressTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_create_a_user_address()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        $addressData = [
            'street' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'is_primary' => true,
        ];

        $response = $this->postJson("/api/users/{$user->id}/addresses", $addressData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'user_id', 'street', 'city', 'state', 'postal_code',
                    'country', 'is_primary', 'created_at', 'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'street' => '123 Main St',
            'city' => 'New York',
        ]);
    }

    /** @test */
    public function it_requires_all_required_fields()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        $response = $this->postJson("/api/users/{$user->id}/addresses", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['street', 'city', 'state', 'postal_code', 'country']);
    }

    /** @test */
    public function it_manages_primary_addresses_correctly()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        // Create a primary address
        $firstAddress = [
            'street' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'is_primary' => true,
        ];

        $this->postJson("/api/users/{$user->id}/addresses", $firstAddress);

        // Create another primary address
        $secondAddress = [
            'street' => '456 Oak Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90001',
            'country' => 'USA',
            'is_primary' => true,
        ];

        $this->postJson("/api/users/{$user->id}/addresses", $secondAddress);

        // The first address should no longer be primary
        $this->assertDatabaseHas('addresses', [
            'street' => '123 Main St',
            'is_primary' => false,
        ]);

        // The second address should be primary
        $this->assertDatabaseHas('addresses', [
            'street' => '456 Oak Ave',
            'is_primary' => true,
        ]);
    }
}