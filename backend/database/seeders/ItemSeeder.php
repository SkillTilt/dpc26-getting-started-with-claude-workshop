<?php

namespace Database\Seeders;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /**
     * Seed the items table.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            // Electronics (category_id: 1)
            [
                'id' => 1,
                'title' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Premium noise-cancelling wireless headphones in excellent condition. Includes original case, charging cable, and audio cable for wired listening.',
                'image_url' => 'items/sony-wh-1000xm5-headphones.webp',
                'category_id' => 1,
                'seller_id' => 1,
                'starting_price' => 80.00,
                'current_price' => 145.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addDays(2),
                'winner_id' => null,
            ],
            [
                'id' => 2,
                'title' => 'iPad Air 5th Gen 64GB',
                'description' => 'Space gray iPad Air with M1 chip, barely used for six months. Ships with original box, charger, and a magnetic folio case.',
                'image_url' => 'items/ipad-air-5th-gen-64gb.png',
                'category_id' => 1,
                'seller_id' => 1,
                'starting_price' => 200.00,
                'current_price' => 340.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addDays(4),
                'winner_id' => null,
            ],
            [
                'id' => 3,
                'title' => 'Mechanical Keyboard Ducky One 3',
                'description' => 'Hot-swappable mechanical keyboard with Cherry MX Brown switches. Full-size layout, RGB backlighting, PBT double-shot keycaps.',
                'image_url' => 'items/mechanical-keyboard-ducky-one-3.png',
                'category_id' => 1,
                'seller_id' => 3,
                'starting_price' => 60.00,
                'current_price' => 60.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addHours(6),
                'winner_id' => null,
            ],
            [
                'id' => 4,
                'title' => 'Samsung Galaxy S23 (cracked screen)',
                'description' => 'Fully functional Galaxy S23 128GB in phantom black. Screen has a hairline crack in the top-left corner but touch works perfectly. Great for repair enthusiasts.',
                'image_url' => 'items/samsung-galaxy-s23-cracked.webp',
                'category_id' => 1,
                'seller_id' => 1,
                'starting_price' => 50.00,
                'current_price' => 92.00,
                'status' => 'closed',
                'ends_at' => $now->copy()->subDay(),
                'winner_id' => 2,
            ],

            // Vintage & Collectibles (category_id: 2)
            [
                'id' => 5,
                'title' => 'Vintage Polaroid SX-70 Camera',
                'description' => 'Iconic 1970s folding SLR instant camera in working condition. Recently serviced with new leather skin. A true collector\'s piece.',
                'image_url' => 'items/vintage-polaroid-sx-70-camera.png',
                'category_id' => 2,
                'seller_id' => 1,
                'starting_price' => 120.00,
                'current_price' => 185.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addDays(3),
                'winner_id' => null,
            ],
            [
                'id' => 6,
                'title' => '1960s Brass Desk Lamp',
                'description' => 'Mid-century modern brass desk lamp with adjustable arm and original patina. Rewired with a new cord for safety. Beautiful warm glow.',
                'image_url' => 'items/1960s-brass-desk-lamp.png',
                'category_id' => 2,
                'seller_id' => 3,
                'starting_price' => 25.00,
                'current_price' => 47.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addDay(),
                'winner_id' => null,
            ],
            [
                'id' => 7,
                'title' => 'First Edition "Neuromancer"',
                'description' => 'First edition, first printing of William Gibson\'s groundbreaking cyberpunk novel. Dust jacket in very good condition with minor shelf wear.',
                'image_url' => 'items/neuromancer.png',
                'category_id' => 2,
                'seller_id' => 1,
                'starting_price' => 150.00,
                'current_price' => 280.00,
                'status' => 'closed',
                'ends_at' => $now->copy()->subDays(3),
                'winner_id' => 2,
            ],
            [
                'id' => 8,
                'title' => 'Antique Pocket Watch (non-working)',
                'description' => 'Beautiful silver-cased pocket watch from the early 1900s. Movement needs repair but the engraved case is in remarkable condition.',
                'image_url' => 'items/antique-pocket-watch.png',
                'category_id' => 2,
                'seller_id' => 2,
                'starting_price' => 30.00,
                'current_price' => 55.00,
                'status' => 'closed',
                'ends_at' => $now->copy()->subDays(5),
                'winner_id' => 3,
            ],

            // Sports & Outdoors (category_id: 3)
            [
                'id' => 9,
                'title' => 'Trek Domane AL 2 Road Bike',
                'description' => 'Entry-level endurance road bike, size 56cm. Shimano Claris groupset, less than 500 miles. Perfect for someone getting into road cycling.',
                'image_url' => 'items/trek-domane-al-2-road-bike.png',
                'category_id' => 3,
                'seller_id' => 2,
                'starting_price' => 300.00,
                'current_price' => 475.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addDays(5),
                'winner_id' => null,
            ],
            [
                'id' => 10,
                'title' => 'Yeti Tundra 45 Cooler',
                'description' => 'Rugged rotomolded cooler in desert tan. Keeps ice for days. A few scuffs from camping trips but fully functional with intact latches and drain plug.',
                'image_url' => 'items/yeti-tundra-45-cooler.png',
                'category_id' => 3,
                'seller_id' => 1,
                'starting_price' => 100.00,
                'current_price' => 155.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addHours(12),
                'winner_id' => null,
            ],
            [
                'id' => 11,
                'title' => 'Patagonia Nano Puff Jacket (M)',
                'description' => 'Lightweight, windproof insulated jacket in forge grey, men\'s medium. Excellent layering piece, worn only a handful of times.',
                'image_url' => 'items/patagonia-nano-puff-jacket.png',
                'category_id' => 3,
                'seller_id' => 2,
                'starting_price' => 60.00,
                'current_price' => 88.00,
                'status' => 'closed',
                'ends_at' => $now->copy()->subDays(2),
                'winner_id' => 1,
            ],
            [
                'id' => 12,
                'title' => 'Wilson Pro Staff Tennis Racket',
                'description' => 'Classic player\'s racket with leather grip, strung with natural gut at 55 lbs. Listing cancelled due to frame crack discovered after posting.',
                'image_url' => 'items/wilson-pro-staff-tennis-racket.png',
                'category_id' => 3,
                'seller_id' => 4,
                'starting_price' => 40.00,
                'current_price' => 40.00,
                'status' => 'cancelled',
                'ends_at' => null,
                'winner_id' => null,
            ],

            // Home & Garden (category_id: 4)
            [
                'id' => 13,
                'title' => 'Le Creuset Dutch Oven 5.5qt',
                'description' => 'Enameled cast-iron dutch oven in flame orange. Used but well-maintained, no chips or cracks. Makes incredible stews and bread.',
                'image_url' => 'items/le-creuset-dutch-oven.png',
                'category_id' => 4,
                'seller_id' => 1,
                'starting_price' => 90.00,
                'current_price' => 142.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addDays(2),
                'winner_id' => null,
            ],
            [
                'id' => 14,
                'title' => 'Dyson V15 Detect (refurb)',
                'description' => 'Manufacturer-refurbished cordless vacuum with laser dust detection. Comes with all original attachments and a 6-month Dyson warranty.',
                'image_url' => 'items/dyson-v15-detect.png',
                'category_id' => 4,
                'seller_id' => 2,
                'starting_price' => 150.00,
                'current_price' => 210.00,
                'status' => 'closed',
                'ends_at' => $now->copy()->subDays(4),
                'winner_id' => 5,
            ],
            [
                'id' => 15,
                'title' => 'Japanese Bonsai Starter Kit',
                'description' => 'Complete kit with three seed varieties (black pine, maple, elm), ceramic pot, soil mix, and illustrated care guide. Great gift for plant lovers.',
                'image_url' => 'items/japanese-bonsai-starter-kit.png',
                'category_id' => 4,
                'seller_id' => 3,
                'starting_price' => 35.00,
                'current_price' => 35.00,
                'status' => 'active',
                'ends_at' => $now->copy()->addHours(8),
                'winner_id' => null,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        // Copy seed images to storage
        $seedImagesPath = '/var/www/seed-images';
        if (File::isDirectory($seedImagesPath)) {
            Storage::disk('public')->makeDirectory('items');
            foreach (File::files($seedImagesPath) as $file) {
                Storage::disk('public')->put(
                    'items/' . $file->getFilename(),
                    File::get($file->getPathname())
                );
            }
        }
    }
}
