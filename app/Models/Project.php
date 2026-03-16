<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title', 
        'slug', 
        'client', 
        'completion_date',
        'gallery', 
        'banner_image',
        'description', 
        'content'
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class);
    }
}
