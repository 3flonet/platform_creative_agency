<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('prefix')
                    ->placeholder('Group')
                    ->helperText('Label prefix (e.g. Group, Phase, Solution)'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
