<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamMemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;
}
