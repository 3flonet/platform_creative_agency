<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Projects/Index', [
            'projects' => Project::with('services')->latest()->get(),
            'settings' => Setting::all()->pluck('value', 'key')->toArray(),
        ]);
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)
            ->with(['services', 'teamMembers'])
            ->firstOrFail();

        // Get related projects (sharing at least one service)
        $serviceIds = $project->services->pluck('id');
        $related = Project::whereHas('services', function($query) use ($serviceIds) {
                $query->whereIn('services.id', $serviceIds);
            })
            ->where('id', '!=', $project->id)
            ->limit(3)
            ->get();

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'related' => $related,
            'settings' => $settings,
        ]);
    }
}
