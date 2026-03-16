<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;

class IconPicker extends Field
{
    protected string $view = 'filament.components.icon-picker';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function getIcons(): array
    {
        $path = app_path('Filament/Resources/Services/uicons.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }
}
