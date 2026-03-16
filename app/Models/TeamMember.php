<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = ['name', 'slug', 'position', 'photo', 'bio', 'instagram', 'linkedin', 'twitter', 'github', 'dribbble', 'sort_order'];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
