<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesRelationManager extends RelationManager
{
    protected static string $relationship = 'listings';

    protected static ?string $title = 'Sales';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed'))
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('Sold Price')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('winner.name')
                    ->label('Buyer'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Closed Date')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
