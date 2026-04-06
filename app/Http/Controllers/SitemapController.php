<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $services = Service::orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $projects = Project::orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $team = TeamMember::orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);
        $articles = Article::published()->orderBy('published_at', 'desc')->get(['slug', 'published_at', 'updated_at']);

        $xml = view('sitemap', compact('services', 'projects', 'team', 'articles'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
