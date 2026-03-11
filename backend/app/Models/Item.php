<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'starting_price',
        'current_price',
        'ends_at',
        'seller_id',
        'category_id',
        'status',
        'winner_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Get the seller of the item.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the winner of the item.
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Get the category of the item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the bids for the item.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Scope a query to only include active items.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include closed items.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope a query to only include items ending within the next 6 hours.
     */
    public function scopeEndingSoon(Builder $query): Builder
    {
        return $query->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addHours(6));
    }

    /**
     * Get the current price formatted as a dollar string.
     */
    public function formattedPrice(): string
    {
        return '$' . number_format((float) $this->current_price, 2);
    }

    /**
     * Determine if the item's auction has ended.
     */
    public function getIsEndedAttribute(): bool
    {
        return $this->ends_at->isPast();
    }
}
