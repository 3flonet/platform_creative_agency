<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->paginated(false)
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('category.title')
                    ->label('Category')
                    ->searchable(),
                TextColumn::make('icon')
                    ->formatStateUsing(fn (string $state): string => "<i class='{$state}'></i>")
                    ->html()
                    ->alignCenter(),
                TextColumn::make('sort_order')
                    ->label('Order'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
