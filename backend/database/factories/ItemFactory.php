<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'image_url' => fake()->optional()->imageUrl(),
            'starting_price' => fake()->randomFloat(2, 5, 500),
            'current_price' => fn (array $attrs) => $attrs['starting_price'],
            'ends_at' => fake()->dateTimeBetween('-7 days', '+7 days'),
            'seller_id' => User::factory(),
            'category_id' => Category::factory(),
            'status' => 'active',
            'winner_id' => null,
        ];
    }
}
