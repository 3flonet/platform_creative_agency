<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
