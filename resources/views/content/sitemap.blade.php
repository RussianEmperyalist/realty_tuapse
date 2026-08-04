{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Static pages --}}
    <url>
        <loc>{{ route('home') }}</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('contacts') }}</loc>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>{{ route('search') }}</loc>
        <priority>0.9</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('news.index') }}</loc>
        <priority>0.7</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('faq') }}</loc>
        <priority>0.6</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('articles.index') }}</loc>
        <priority>0.6</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('gallery.index') }}</loc>
        <priority>0.5</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>{{ route('contact.form') }}</loc>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('callback.form') }}</loc>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('review') }}</loc>
        <priority>0.4</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('favorites') }}</loc>
        <priority>0.3</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('information') }}</loc>
        <priority>0.4</priority>
        <changefreq>monthly</changefreq>
    </url>

    {{-- City pages --}}
    <url>
        <loc>{{ route('city.tuapse') }}</loc>
        <priority>0.7</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>{{ route('city.tuapsinsky') }}</loc>
        <priority>0.7</priority>
        <changefreq>weekly</changefreq>
    </url>

    {{-- Dynamic content: Properties --}}
    @foreach ($properties as $property)
        <url>
            <loc>{{ route('properties.show', $property->slug) }}</loc>
            <lastmod>{{ $property->updated_at->toIso8601String() }}</lastmod>
            <priority>0.8</priority>
            <changefreq>daily</changefreq>
        </url>
    @endforeach

    {{-- Dynamic content: News --}}
    @foreach ($news as $item)
        <url>
            <loc>{{ route('news.show', $item->slug) }}</loc>
            <lastmod>{{ $item->updated_at->toIso8601String() }}</lastmod>
            <priority>0.6</priority>
            <changefreq>weekly</changefreq>
        </url>
    @endforeach

    {{-- Dynamic content: FAQ --}}
    @foreach ($faqEntries as $entry)
        <url>
            <loc>{{ route('faq.show', $entry['slug']) }}</loc>
            <priority>0.5</priority>
            <changefreq>monthly</changefreq>
        </url>
    @endforeach

    {{-- Dynamic content: Articles --}}
    @foreach ($articles as $article)
        <url>
            <loc>{{ route('articles.show', $article['slug']) }}</loc>
            <priority>0.5</priority>
            <changefreq>monthly</changefreq>
        </url>
    @endforeach
</urlset>
