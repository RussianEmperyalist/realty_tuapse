<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Render the search results page.
     */
    public function __invoke(Request $request): View
    {
        $listMode = in_array($request->string('ls')->value(), ['block', 'table', 'map'], true)
            ? $request->string('ls')->value()
            : 'block';

        $properties = Property::query()
            ->with(['employee', 'images'])
            ->where('is_published', true)
            ->when($request->filled('city'), function (Builder $query) use ($request): void {
                $cityMap = [
                    '9' => 'tuapse',
                    '10' => 'tuapsinskij-rajon',
                ];

                $cities = collect((array) $request->input('city', []))
                    ->map(fn ($value) => $cityMap[(string) $value] ?? null)
                    ->filter()
                    ->values()
                    ->all();

                if ($cities !== []) {
                    $query->whereIn('city', $cities);
                }
            })
            ->when($request->filled('apType'), function (Builder $query) use ($request): void {
                $dealType = match ((string) $request->input('apType')) {
                    '1' => 'rent',
                    '2' => 'sale',
                    default => null,
                };

                if ($dealType !== null) {
                    $query->where('deal_type', $dealType);
                }
            })
            ->when($request->filled('objType'), function (Builder $query) use ($request): void {
                $propertyType = match ((string) $request->input('objType')) {
                    '1' => 'apartment',
                    '2' => 'house',
                    '3' => 'room',
                    '4' => 'land',
                    '5' => 'new_building',
                    '6' => 'hotel',
                    '8' => 'garage',
                    '9' => 'commercial',
                    default => null,
                };

                if ($propertyType !== null) {
                    $query->where('property_type', $propertyType);
                }
            })
            ->when($request->filled('price_min'), fn (Builder $query) => $query->where('price', '>=', (int) $request->input('price_min')))
            ->when($request->filled('price_max'), fn (Builder $query) => $query->where('price', '<=', (int) $request->input('price_max')))
            ->when($request->filled('square_min'), fn (Builder $query) => $query->where('square', '>=', (float) $request->input('square_min')))
            ->when($request->filled('square_max'), fn (Builder $query) => $query->where('square', '<=', (float) $request->input('square_max')))
            ->when($request->filled('rooms'), function (Builder $query) use ($request): void {
                $rooms = (int) $request->input('rooms');

                if ($rooms > 0 && $rooms < 4) {
                    $query->where('rooms', $rooms);
                }

                if ($rooms === 4) {
                    $query->where('rooms', '>=', 4);
                }
            })
            ->when($request->filled('floor_min'), fn (Builder $query) => $query->where('floor', '>=', (int) $request->input('floor_min')))
            ->when($request->filled('floor_max'), fn (Builder $query) => $query->where('floor', '<=', (int) $request->input('floor_max')))
            ->when($request->filled('term'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->input('term'));

                $query->where(function (Builder $innerQuery) use ($term): void {
                    $innerQuery
                        ->where('title', 'like', '%' . $term . '%')
                        ->orWhere('address', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%');
                });
            })
            ->when($request->filled('sApId'), fn (Builder $query) => $query->where('legacy_id', (int) $request->input('sApId')))
            ->when($request->boolean('wp'), fn (Builder $query) => $query->whereHas('images'))
            ->when($request->boolean('featured'), fn (Builder $query) => $query->where('is_featured', true))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('search.index', [
            'bodyClass' => 'inner_page',
            'properties' => $properties,
            'listMode' => $listMode,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Поиск недвижимости'],
            ],
        ]);
    }
}
