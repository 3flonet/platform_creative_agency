<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\DocumentationController;

Route::get('/docs', [DocumentationController::class, 'index'])->name('documentation');

Route::get('/', function () {
    $isInstalled = file_exists(storage_path('installed.lock'));
    
    // 🛡️ Resilience for Web Installer: Use lock file instead of Schema::hasTable to avoid DB calls
    $settings = [];
    if ($isInstalled) {
        $settings = \App\Models\Setting::whereIn('key', [
            'site_name', 'site_logo', 'site_favicon', 'site_description',
            'hero_title_1', 'hero_title_2', 'hero_tagline',
            'section_1_label', 'section_2_label', 'section_3_label', 'section_4_label', 'section_5_label', 'journal_label',
            'section_2_title', 'section_3_title', 'section_4_title', 'journal_title', 'section_5_title',
            'contact_email', 'contact_phone', 'contact_address',
            'footer_text', 'geo_placename',
            'social_instagram', 'social_linkedin', 'social_facebook', 'social_youtube', 'social_threads', 'social_github', 'twitter_handle',
            'intro_sequence', 'intro_brand', 'intro_speed', 'intro_use_logo', 'intro_logo',
            '3d_object_type', '3d_model_custom', '3d_animate_model',
            '3d_pos_1', '3d_pos_2', '3d_pos_3', '3d_pos_4', '3d_pos_5', '3d_pos_6',
            '3d_rot_1', '3d_rot_2', '3d_rot_3', '3d_rot_4', '3d_rot_5', '3d_rot_6',
            '3d_scale_1', '3d_scale_2', '3d_scale_3', '3d_scale_4', '3d_scale_5', '3d_scale_6'
        ])->pluck('value', 'key')->toArray();
    }

    return Inertia::render('Welcome', [
        'services' => $isInstalled ? \App\Models\Service::with('category')->orderBy('sort_order')->get() : [],
        'projects' => $isInstalled ? \App\Models\Project::with('services')->latest()->get() : [],
        'team' => $isInstalled ? \App\Models\TeamMember::orderBy('sort_order')->get() : [],
        'featuredArticles' => $isInstalled ? \App\Models\Article::published()->featured()->latest('published_at')->take(3)->get() : [],
        'settings' => $settings,
    ]);
});

Route::get('/journal', [ArticleController::class, 'index'])->name('journal.index');
Route::get('/journal/{slug}', [ArticleController::class, 'show'])->name('journal.show');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/contact', function () {
    $isInstalled = file_exists(storage_path('installed.lock'));
    $settings = [];
    if ($isInstalled) {
        $settings = \App\Models\Setting::whereIn('key', [
            'site_name', 'site_logo', 'site_favicon', 'site_description',
            'contact_email', 'contact_phone', 'contact_address',
            'footer_text', 'geo_placename',
            'social_instagram', 'social_linkedin', 'social_facebook', 'social_youtube', 'social_threads', 'social_github', 'twitter_handle',
            'section_1_label', 'section_2_label', 'section_3_label', 'section_4_label', 'section_5_label', 'journal_label'
        ])->pluck('value', 'key')->toArray();
    }

    return Inertia::render('Contact', [
        'settings' => $settings,
    ]);
})->name('contact');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/team', [TeamMemberController::class, 'index'])->name('team.index');
Route::get('/team/{slug}', [TeamMemberController::class, 'show'])->name('team.show');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

// Admin CSV Export (protected by auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/export/traffic', [\App\Http\Controllers\ExportController::class, 'trafficCsv'])->name('admin.export.traffic');
});

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Web Installer
Route::prefix('install')->group(function () {
    Route::get('/', [InstallerController::class, 'index'])->name('install.index');
    Route::post('/requirements', [InstallerController::class, 'checkRequirements']);
    Route::post('/database', [InstallerController::class, 'setupDatabase']);
    Route::post('/license', [InstallerController::class, 'verifyLicense']);
    Route::post('/migrate', [InstallerController::class, 'runMigrations']);
    Route::post('/finalize', [InstallerController::class, 'finalize']);
});
