<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('position')
                    ->required(),
                FileUpload::make('photo')
                    ->image()
                    ->directory('team')
                    ->disk('public')
                    ->columnSpanFull(),
                Textarea::make('bio')
                    ->rows(3)
                    ->columnSpanFull(),
                
                TextInput::make('instagram')
                    ->prefix('https://instagram.com/'),
                TextInput::make('linkedin')
                    ->prefix('https://linkedin.com/in/'),
                TextInput::make('twitter')
                    ->label('Twitter (X)')
                    ->prefix('https://x.com/'),
                TextInput::make('github')
                    ->prefix('https://github.com/'),
                TextInput::make('dribbble')
                    ->prefix('https://dribbble.com/'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
