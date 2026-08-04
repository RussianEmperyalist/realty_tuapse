<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\NewsPost;
use App\Models\Property;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function __invoke(): View
    {
        return view('home', [
            'bodyClass' => '',
            'featuredProperties' => Property::query()
                ->with(['employee', 'images'])
                ->where('is_published', true)
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->limit(30)
                ->get(),
            'directionCards' => $this->directionCards(),
            'latestNews' => NewsPost::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
            'employees' => Employee::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
        ]);
    }

    /**
     * Direction cards shown on the legacy home page.
     *
     * @return list<array<string, mixed>>
     */
    private function directionCards(): array
    {
        $counts = Property::query()
            ->where('is_published', true)
            ->selectRaw('city, property_type, count(*) as aggregate')
            ->groupBy('city', 'property_type')
            ->get()
            ->reduce(function (array $carry, Property $property): array {
                $city = (string) $property->city;
                $type = (string) $property->property_type;

                $carry[$city][$type] = (int) $property->aggregate;

                return $carry;
            }, []);

        $types = [
            ['key' => 'apartment', 'label' => 'квартира', 'path' => 'kvartira', 'objType' => 1],
            ['key' => 'room', 'label' => 'комната', 'path' => 'komnata', 'objType' => 3],
            ['key' => 'house', 'label' => 'дом', 'path' => 'dom', 'objType' => 2],
            ['key' => 'land', 'label' => 'земельный участок', 'path' => 'zemelnyj-uchastok', 'objType' => 4],
            ['key' => 'new_building', 'label' => 'новостройка', 'path' => 'mnogokvartirnyj-dom', 'objType' => 5],
            ['key' => 'hotel', 'label' => 'гостиница', 'path' => 'gostinica', 'objType' => 6],
            ['key' => 'garage', 'label' => 'гараж', 'path' => 'garazh', 'objType' => 8],
            ['key' => 'commercial', 'label' => 'коммерция', 'path' => 'kommerciya', 'objType' => 9],
        ];

        return [
            [
                'title' => 'Туапсе',
                'image' => 'legacy/uploads/models/2024/05/thumb_640x440_1-391.jpg',
                'url' => route('city.tuapse'),
                'city' => 'tuapse',
                'legacy_city_id' => 9,
                'types' => $this->directionTypeLinks('tuapse', 'tuapse', $types, $counts['tuapse'] ?? []),
            ],
            [
                'title' => 'Туапсинский район',
                'image' => 'legacy/uploads/models/2024/04/thumb_640x440_888.jpg',
                'url' => route('city.tuapsinsky'),
                'city' => 'tuapsinskij-rajon',
                'legacy_city_id' => 10,
                'types' => $this->directionTypeLinks('tuapsinskij-rajon', 'tuapsinskij-rajon', $types, $counts['tuapsinskij-rajon'] ?? []),
            ],
            [
                'title' => 'Другие города',
                'image' => 'legacy/uploads/models/2024/04/thumb_640x440_.jpg',
                'url' => route('search', ['city' => [11]]),
                'city' => null,
                'legacy_city_id' => 11,
                'types' => $this->directionTypeLinks(null, null, $types, []),
            ],
        ];
    }

    /**
     * @param list<array{key:string,label:string,path:string,objType:int}> $types
     * @param array<string, int> $counts
     *
     * @return list<array<string, mixed>>
     */
    private function directionTypeLinks(?string $routePrefix, ?string $city, array $types, array $counts): array
    {
        return array_map(function (array $type) use ($routePrefix, $city, $counts): array {
            $count = $counts[$type['key']] ?? 0;

            return [
                'label' => $type['label'],
                'count' => $count,
                'is_active' => $count > 0,
                'url' => $routePrefix !== null
                    ? url($routePrefix . '/' . $type['path'])
                    : route('search', ['city' => [11], 'objType' => $type['objType']]),
            ];
        }, $types);
    }
}
