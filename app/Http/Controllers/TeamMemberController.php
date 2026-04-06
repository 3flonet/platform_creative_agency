<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index()
    {
        return Inertia::render('Teams/Index', [
            'team' => TeamMember::orderBy('sort_order')->get(),
            'settings' => Setting::all()->pluck('value', 'key')->toArray(),
        ]);
    }

    public function show($slug)
    {
        $member = TeamMember::where('slug', $slug)
            ->with(['projects' => function($query) {
                $query->latest();
            }])
            ->firstOrFail();

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Teams/Show', [
            'member' => $member,
            'settings' => $settings,
        ]);
    }
}
