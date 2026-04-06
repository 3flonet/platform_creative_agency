<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3]) // Set root grid to 3 columns
            ->components([
                // Main Column (Left - 2/3 width)
                Grid::make(1)
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('General Content')
                            ->icon('heroicon-o-document-text')
                            ->description('Enter the primary details of the journal entry.')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Article Title')
                                    ->placeholder('Enter a compelling headline...')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                
                                TextInput::make('slug')
                                    ->label('URL Identifier')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->prefix('journal/')
                                    ->helperText('This controls the URL of the article.'),

                                RichEditor::make('content')
                                    ->label('Story Narrative')
                                    ->required()
                                    ->columnSpanFull()
                                    ->extraInputAttributes(['style' => 'min-height: 520px']),
                            ]),
                    ]),

                // Sidebar Column (Right - 1/3 width)
                Grid::make(1)
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Publishing Details')
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->schema([
                                Select::make('article_category_id')
                                    ->label('Classification')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->native(false),

                                DateTimePicker::make('published_at')
                                    ->label('Display Date')
                                    ->default(now()),

                                Toggle::make('is_featured')
                                    ->label('Promote to Homepage')
                                    ->inline(false)
                                    ->onColor('danger'),
                            ]),

                        Section::make('Media Asset')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->label('Cover Image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('articles')
                                    ->disk('public'),
                            ]),

                        Section::make('Search Engine Optimizer')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_description')
                                    ->label('SEO Description')
                                    ->maxLength(160),
                                    
                                TextInput::make('meta_keywords')
                                    ->label('Meta Tags')
                                    ->placeholder('creative, insight, movement'),
                            ]),
                    ]),
            ]);
    }
}
