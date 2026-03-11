<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_list_active_items_in_category()
    {
        $category = Category::factory()->create();
        $seller = User::factory()->create();

        Item::factory()->count(3)->create([
            'category_id' => $category->id,
            'seller_id' => $seller->id,
            'status' => 'active',
            'ends_at' => now()->addDay(),
        ]);

        // Closed item should not appear
        Item::factory()->create([
            'category_id' => $category->id,
            'seller_id' => $seller->id,
            'status' => 'closed',
            'ends_at' => now()->subDay(),
        ]);

        $response = $this->getJson("/api/categories/{$category->slug}/items");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'current_price', 'ends_at', 'seller', 'bids_count'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_category_items_sorted_by_ending_soonest()
    {
        $category = Category::factory()->create();
        $seller = User::factory()->create();

        $endingSoon = Item::factory()->create([
            'category_id' => $category->id,
            'seller_id' => $seller->id,
            'status' => 'active',
            'ends_at' => now()->addHours(1),
        ]);

        $endingLater = Item::factory()->create([
            'category_id' => $category->id,
            'seller_id' => $seller->id,
            'status' => 'active',
            'ends_at' => now()->addDays(3),
        ]);

        $response = $this->getJson("/api/categories/{$category->slug}/items");

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($endingSoon->id, $data[0]['id']);
        $this->assertEquals($endingLater->id, $data[1]['id']);
    }

    public function test_category_items_returns_404_for_nonexistent_category()
    {
        $response = $this->getJson('/api/categories/nonexistent-slug/items');

        $response->assertStatus(404);
    }

    public function test_category_items_rejects_invalid_page_parameter()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->slug}/items?page=abc");

        $response->assertStatus(422);
    }
}
