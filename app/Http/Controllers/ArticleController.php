<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->with('category')->latest('published_at');

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        return Inertia::render('Journal/Index', [
            'articles' => $query->paginate(9)->withQueryString(),
            'categories' => ArticleCategory::all(),
            'settings' => Setting::pluck('value', 'key')->toArray(),
        ]);
    }

    public function show(string $slug)
    {
        /** @var \App\Models\Article $article */
        $article = Article::published()->with('category')->where('slug', $slug)->firstOrFail();
        
        $categoryId = $article->article_category_id;
        
        // Relasi artikel lain dari kategori yang sama
        $relatedArticles = $categoryId ? Article::published()
            ->where('article_category_id', $categoryId)
            ->where('id', '!=', $article->id)
            ->limit(3)
            ->get() : collect();

        return Inertia::render('Journal/Show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'settings' => Setting::pluck('value', 'key')->toArray(),
        ]);
    }
}
