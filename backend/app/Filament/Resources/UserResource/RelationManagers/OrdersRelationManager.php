<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'wonItems';

    protected static ?string $title = 'Orders';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('Price Paid')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller'),
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
