<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Setting;

class DocumentationController extends Controller
{
    public function index()
    {
        $docPath = base_path('DOCUMENTATION.md');
        $content = File::exists($docPath) ? File::get($docPath) : '# Documentation not found.';
        
        // Convert Markdown to HTML
        $htmlContent = Str::markdown($content);

        $isInstalled = file_exists(storage_path('installed.lock'));
        $settings = [];
        if ($isInstalled) {
            $settings = Setting::whereIn('key', [
                'site_name', 'site_logo', 'site_favicon', 'site_description',
                'contact_email', 'contact_phone', 'contact_address',
                'footer_text', 'geo_placename',
                'social_instagram', 'social_linkedin', 'social_facebook', 'social_youtube', 'social_threads', 'social_github', 'twitter_handle',
                'section_1_label', 'section_2_label', 'section_3_label', 'section_4_label', 'section_5_label', 'journal_label'
            ])->pluck('value', 'key')->toArray();
        }

        return Inertia::render('Documentation', [
            'content' => $htmlContent,
            'settings' => $settings,
        ]);
    }
}
