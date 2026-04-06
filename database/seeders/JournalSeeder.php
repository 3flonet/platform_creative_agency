<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name' => 'Design Process', 'description' => 'Inside our creative studio and workflows.'],
            ['name' => 'Industry Insights', 'description' => 'Market trends and digital movement analysis.'],
            ['name' => 'Tech & Innovation', 'description' => 'Exploring the boundaries of web and 3D tech.'],
        ];

        foreach ($categories as $cat) {
            ArticleCategory::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'description' => $cat['description']]
            );
        }

        $designCat = ArticleCategory::where('slug', 'design-process')->first();
        $techCat = ArticleCategory::where('slug', 'tech-innovation')->first();
        $industryCat = ArticleCategory::where('slug', 'industry-insights')->first();

        // 2. Create Sample Articles
        $articles = [
            [
                'article_category_id' => $designCat->id,
                'title' => 'Beyond the Canvas: Our 3D Design Philosophy',
                'content' => '<h2>The New Dimension</h2><p>In the world of 3flo, we don’t just build websites; we create immersive journeys. Our design process starts with a blank void and ends with a living, breathing digital universe.</p><p>We believe that "Non-stop Creative" means constantly pushing the boundaries of what is possible in a browser. By integrating Three.js and high-end animations, we bring a level of tangibility to the digital screen that was previously reserved for cinema.</p>',
                'is_featured' => true,
                'status' => 'published',
                'meta_description' => 'Discover how 3flo integrates 3D technology into modern brand narratives.',
                'meta_keywords' => '3d design, creative agency, branding, threejs',
                'published_at' => now(),
            ],
            [
                'article_category_id' => $techCat->id,
                'title' => 'The Future of Immersive Web Experiences',
                'content' => '<h2>Web 3.0 Aesthetic</h2><p>As hardware becomes more powerful, the web is shifting from flat documents to spatial environments. At 3flo, we are at the forefront of this movement.</p><p>Using technologies like React Three Fiber and GSAP, we synchronize user interactions with complex 3D simulations. The result is a seamless transition between content and experience.</p>',
                'is_featured' => true,
                'status' => 'published',
                'meta_description' => 'Exploring the evolution of web design from flat to immersive spatial experiences.',
                'meta_keywords' => 'future web, immersive experience, r3f, gsap',
                'published_at' => now()->subDays(2),
            ],
            [
                'article_category_id' => $industryCat->id,
                'title' => 'Why Branding Needs a Creative Loop',
                'content' => '<h2>The Creative Loop</h2><p>Static brands are dying. To survive in the modern era, a brand must be a "Loop"—a cycle of discovery, ideation, and movement that never stops.</p><p>3flo helps brands find their visual soul and convert it into a digital engine that powers their entire business ecosystem.</p>',
                'is_featured' => true,
                'status' => 'published',
                'meta_description' => 'Learn why the traditional linear branding model is being replaced by a creative loop.',
                'meta_keywords' => 'branding, marketing, strategy, creative loop',
                'published_at' => now()->subDays(5),
            ],
            [
                'article_category_id' => $designCat->id,
                'title' => 'Minimalism vs Maximalism in Digital Spaces',
                'content' => '<h2>Choosing the Right Path</h2><p>The debate between minimal and maximal design is as old as design itself. However, in immersive spaces, the rules are different...</p>',
                'is_featured' => false,
                'status' => 'published',
                'meta_description' => 'A deep dive into the balance of visual weight in high-end creative websites.',
                'meta_keywords' => 'minimalism, maximalism, web design',
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(
                ['slug' => Str::slug($art['title'])],
                $art
            );
        }
    }
}
