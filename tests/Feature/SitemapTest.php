<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_xml_returns_valid_response(): void
    {
        Property::factory()->create([
            'slug' => 'test-property',
            'title' => 'Test Property',
            'is_published' => true,
        ]);

        NewsPost::factory()->create([
            'slug' => 'test-news',
            'title' => 'Test News',
            'is_published' => true,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
    }

    public function test_sitemap_contains_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();

        $this->assertStringContainsString('<loc>' . route('home') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . route('contacts') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . route('search') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . route('news.index') . '</loc>', $content);
    }

    public function test_sitemap_contains_published_properties(): void
    {
        $property = Property::factory()->create([
            'slug' => 'test-property-123',
            'title' => 'Test Property',
            'is_published' => true,
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();

        $this->assertStringContainsString(
            '<loc>' . route('properties.show', $property->slug) . '</loc>',
            $content
        );
    }

    public function test_sitemap_does_not_contain_unpublished_properties(): void
    {
        $property = Property::factory()->create([
            'slug' => 'unpublished-property',
            'title' => 'Unpublished Property',
            'is_published' => false,
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();

        $this->assertStringNotContainsString(
            route('properties.show', $property->slug),
            $content
        );
    }

    public function test_sitemap_xml_is_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();

        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString('</urlset>', $content);
    }

    public function test_sitemap_returns_valid_xml_structure(): void
    {
        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();

        // Simple XML validation
        $xml = simplexml_load_string($content);
        $this->assertNotFalse($xml);
        $this->assertEquals('urlset', $xml->getName());
    }
}
