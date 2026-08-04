<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Show the property page.
     */
    public function show(Property $property): View
    {
        $property->load(['employee', 'images']);

        $similarProperties = Property::query()
            ->with(['employee', 'images'])
            ->where('is_published', true)
            ->whereKeyNot($property->id)
            ->where('property_type', $property->property_type)
            ->when($property->city, fn ($query) => $query->where('city', $property->city))
            ->limit(4)
            ->get();

        return view('properties.show', [
            'bodyClass' => 'inner_page',
            'property' => $property,
            'similarProperties' => $similarProperties,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Поиск недвижимости', 'url' => route('search')],
                ['label' => $property->title],
            ],
        ]);
    }
}
