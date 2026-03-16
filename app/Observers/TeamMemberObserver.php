<?php

namespace App\Observers;

use App\Services\ImageOptimizer;

class TeamMemberObserver
{
    public function saved($member): void
    {
        if ($member->photo && $member->wasChanged('photo')) {
            ImageOptimizer::optimize($member->photo, 'public', 85, 800);
        }
    }
}
