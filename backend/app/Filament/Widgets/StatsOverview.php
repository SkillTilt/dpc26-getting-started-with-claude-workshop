<?php

namespace App\Filament\Widgets;

use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Auctions', Item::where('status', 'active')->count()),
            Stat::make('Total Bids Today', Bid::whereDate('created_at', today())->count()),
            Stat::make('Revenue', '$' . number_format(Item::where('status', 'closed')->sum('current_price'), 2)),
            Stat::make('New Users (This Week)', User::where('created_at', '>=', now()->subWeek())->count()),
        ];
    }
}
