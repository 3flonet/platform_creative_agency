<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 2])
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('client')
                    ->placeholder('e.g. CyberPulse Tech'),
                
                DatePicker::make('completion_date')
                    ->label('Project Date'),

                Select::make('services')
                    ->relationship('services', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),

                Select::make('teamMembers')
                    ->label('The Collective (Team Members)')
                    ->relationship('teamMembers', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),

                FileUpload::make('banner_image')
                    ->label('Cinematic Hero Banner')
                    ->image()
                    ->directory('projects/banner')
                    ->disk('public')
                    ->imageEditor()
                    ->columnSpanFull(),

                FileUpload::make('gallery')
                    ->label('Project Gallery (Artifacts)')
                    ->multiple()
                    ->image()
                    ->directory('projects/gallery')
                    ->disk('public')
                    ->reorderable()
                    ->openable()
                    ->downloadable()
                    ->panelLayout('grid')
                    ->imageEditor()
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Executive Summary')
                    ->placeholder('A short evocative summary of the project...')
                    ->rows(3)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Project Narrative')
                    ->placeholder('Describe the challenge, process, and results...')
                    ->columnSpanFull(),
            ]);
    }
}
