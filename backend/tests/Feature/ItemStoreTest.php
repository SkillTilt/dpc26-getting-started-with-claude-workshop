<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_item()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/items', [
                'title' => 'Vintage Camera',
                'description' => 'A beautiful vintage camera in great condition.',
                'starting_price' => 49.99,
                'category_id' => $category->id,
                'duration' => 3,
            ]);

        $response->assertStatus(201);
    }

    public function test_unauthenticated_user_cannot_create_item()
    {
        $response = $this->postJson('/api/items', [
            'title' => 'Vintage Camera',
            'description' => 'A beautiful vintage camera.',
            'starting_price' => 49.99,
            'category_id' => 1,
            'duration' => 3,
        ]);

        $response->assertStatus(401);
    }

    public function test_item_creation_requires_valid_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/items', []);

        $response->assertStatus(422);
    }
}
