<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Static pages --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/services') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/projects') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/team') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/journal') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- Services --}}
    @foreach($services as $service)
    <url>
        <loc>{{ url('/services/' . $service->slug) }}</loc>
        <lastmod>{{ $service->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Projects --}}
    @foreach($projects as $project)
    <url>
        <loc>{{ url('/projects/' . $project->slug) }}</loc>
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Team --}}
    @foreach($team as $member)
    <url>
        <loc>{{ url('/team/' . $member->slug) }}</loc>
        <lastmod>{{ $member->updated_at->toAtomString() }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    {{-- Articles --}}
    @foreach($articles as $article)
    <url>
        <loc>{{ url('/journal/' . $article->slug) }}</loc>
        <lastmod>{{ ($article->published_at ?? $article->updated_at)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
