<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Services/Index', [
            'services' => Service::with('category')->orderBy('sort_order')->get(),
            'settings' => Setting::all()->pluck('value', 'key')->toArray(),
        ]);
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->with(['category', 'projects' => function($query) {
                $query->latest()->limit(4);
            }])
            ->firstOrFail();
        
        // Get related services (same category)
        $related = Service::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->limit(3)
            ->get();

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Services/Show', [
            'service' => $service,
            'related' => $related,
            'settings' => $settings,
        ]);
    }
}
