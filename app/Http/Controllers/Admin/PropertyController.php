<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Support\ImageStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage,
    ) {
    }

    /**
     * Display a listing of properties.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $effectiveEmployeeId = $this->effectiveEmployeeId($request);

        $properties = Property::query()
            ->with(['employee', 'images'])
            ->when($user?->isAdmin() && $request->filled('employee') && !$this->isEmployeeModeActive($request), function (Builder $query) use ($request): void {
                $query->where('employee_id', (int) $request->integer('employee'));
            })
            ->when($effectiveEmployeeId, fn (Builder $query) => $query->where('employee_id', $effectiveEmployeeId))
            ->when($request->filled('term'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->input('term'));
                $query->where(function (Builder $nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('title', 'like', '%' . $term . '%')
                        ->orWhere('address', 'like', '%' . $term . '%')
                        ->orWhere('legacy_id', 'like', '%' . $term . '%');
                });
            })
            ->orderByDesc('published_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.properties.index', [
            'properties' => $properties,
            'employees' => Employee::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'selectedEmployeeId' => (int) $request->integer('employee'),
            'effectiveEmployeeId' => $effectiveEmployeeId,
            'isEmployeeModeActive' => $this->isEmployeeModeActive($request),
        ]);
    }

    /**
     * Show the form for creating a new property.
     */
    public function create(Request $request): View
    {
        $selectedEmployeeId = $this->effectiveEmployeeId($request) ?: (int) $request->integer('employee');

        return view('admin.properties.form', [
            'property' => new Property([
                'deal_type' => 'sale',
                'property_type' => 'apartment',
                'currency' => 'руб.',
                'city' => 'tuapse',
                'is_published' => true,
                'published_at' => now(),
            ]),
            'employees' => $this->availableEmployees($request),
            'selectedEmployeeId' => $selectedEmployeeId,
            'formAction' => route('admin.properties.store'),
            'method' => 'post',
        ]);
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['employee_id'] = $this->resolveEmployeeId($request, $data['employee_id'] ?? null);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title']);
        $data['legacy_id'] = ($data['legacy_id'] ?? null) ?: ((int) Property::query()->max('legacy_id') + 1);

        $property = Property::query()->create($data);
        $this->syncPropertyImages($request, $property);

        return redirect()
            ->route('admin.properties.edit', $property)
            ->with('status', 'Объект создан.');
    }

    /**
     * Show the form for editing a property.
     */
    public function edit(Request $request, Property $property): View
    {
        $this->authorizeProperty($request, $property);
        $property->load('images');

        return view('admin.properties.form', [
            'property' => $property,
            'employees' => $this->availableEmployees($request),
            'selectedEmployeeId' => $property->employee_id,
            'formAction' => route('admin.properties.update', $property),
            'method' => 'put',
        ]);
    }

    /**
     * Update the property.
     */
    public function update(Request $request, Property $property): RedirectResponse
    {
        $this->authorizeProperty($request, $property);

        $data = $this->validatedData($request, $property);
        $data['employee_id'] = $this->resolveEmployeeId($request, $data['employee_id'] ?? $property->employee_id);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['title'], $property->id);
        $data['legacy_id'] = ($data['legacy_id'] ?? null) ?: $property->legacy_id;

        $property->update($data);
        $this->syncPropertyImages($request, $property);

        return redirect()
            ->route('admin.properties.edit', $property)
            ->with('status', 'Объект обновлен.');
    }

    /**
     * Delete the property.
     */
    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $this->authorizeProperty($request, $property);
        $property->load('images');

        foreach ($property->images as $image) {
            $this->deletePublicPath($image->path);
            $this->deletePublicPath($image->thumb_path);
        }

        $property->delete();

        return redirect()
            ->route('admin.properties.index')
            ->with('status', 'Объект удален.');
    }

    /**
     * Validation rules for property data.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Property $property = null): array
    {
        return $request->validate([
            'legacy_id' => ['nullable', 'integer', 'unique:properties,legacy_id' . ($property ? ',' . $property->id : '')],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:properties,slug' . ($property ? ',' . $property->id : '')],
            'deal_type' => ['required', 'string', 'max:32'],
            'property_type' => ['required', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'price_label' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:16'],
            'rooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'floor' => ['nullable', 'integer', 'min:0', 'max:100'],
            'floors_total' => ['nullable', 'integer', 'min:0', 'max:100'],
            'square' => ['nullable', 'numeric', 'min:0'],
            'windows' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'phone_override' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'images.*' => ['nullable', 'image', 'max:8192'],
            'images' => ['required_without:delete_images', 'array', 'max:10'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:property_images,id'],
            'cover_image_id' => ['nullable', 'integer', 'exists:property_images,id'],
        ]);
    }

    /**
     * Upload and synchronize property images.
     */
    private function syncPropertyImages(Request $request, Property $property): void
    {
        $property->load('images');

        foreach ((array) $request->input('delete_images', []) as $imageId) {
            $image = $property->images->firstWhere('id', (int) $imageId);
            if ($image === null) {
                continue;
            }

            $this->deletePublicPath($image->path);
            $this->deletePublicPath($image->thumb_path);
            $image->delete();
        }

        if ($request->hasFile('images')) {
            $sortOrder = ((int) PropertyImage::query()->where('property_id', $property->id)->max('sort_order')) + 1;
            foreach ($request->file('images', []) as $file) {
                if ($file === null) {
                    continue;
                }

                $storedImage = $this->imageStorage->storePublicImageWithThumbnail(
                    $file,
                    'properties',
                    'properties/thumbs',
                    820,
                    428,
                );

                PropertyImage::query()->create([
                    'property_id' => $property->id,
                    'path' => $storedImage['path'],
                    'thumb_path' => $storedImage['thumb_path'],
                    'alt' => $property->title,
                    'sort_order' => $sortOrder++,
                    'is_cover' => false,
                ]);
            }
        }

        $coverImageId = (int) $request->integer('cover_image_id');
        $images = PropertyImage::query()->where('property_id', $property->id)->orderBy('sort_order')->get();
        if ($images->isEmpty()) {
            return;
        }

        $resolvedCoverId = $coverImageId ?: $images->first()->id;
        foreach ($images as $image) {
            $image->forceFill(['is_cover' => $image->id === $resolvedCoverId])->save();
        }
    }

    /**
     * Employees available in the current admin context.
     */
    private function availableEmployees(Request $request)
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return Employee::query()->where('is_active', true)->orderBy('sort_order')->get();
        }

        return Employee::query()
            ->whereKey($user?->employee?->id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Determine the effective employee for the session.
     */
    private function effectiveEmployeeId(Request $request): ?int
    {
        $user = $request->user();

        if ($user === null) {
            return null;
        }

        if (!$user->isAdmin()) {
            return $user->employee?->id;
        }

        $employeeId = (int) $request->session()->get('admin_employee_mode');

        return $employeeId > 0 ? $employeeId : null;
    }

    /**
     * Check whether the session is in employee mode.
     */
    private function isEmployeeModeActive(Request $request): bool
    {
        return $this->effectiveEmployeeId($request) !== null && $request->user()?->isAdmin();
    }

    /**
     * Resolve the employee id with role restrictions applied.
     */
    private function resolveEmployeeId(Request $request, mixed $employeeId): ?int
    {
        $resolved = (int) $employeeId;
        $effectiveEmployeeId = $this->effectiveEmployeeId($request);

        if ($effectiveEmployeeId !== null) {
            return $effectiveEmployeeId;
        }

        if ($request->user()?->isAdmin()) {
            return $resolved ?: null;
        }

        return $request->user()?->employee?->id;
    }

    /**
     * Ensure the current user can work with the property.
     */
    private function authorizeProperty(Request $request, Property $property): void
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            $effectiveEmployeeId = $this->effectiveEmployeeId($request);
            if ($effectiveEmployeeId !== null && $property->employee_id !== $effectiveEmployeeId) {
                abort(403);
            }

            return;
        }

        abort_if($property->employee_id !== $user?->employee?->id, 403);
    }

    /**
     * Create a unique slug for the property.
     */
    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug !== '' ? $baseSlug : 'object';
        $counter = 1;

        while (
            Property::query()
                ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Delete a file if it belongs to the public storage disk.
     */
    private function deletePublicPath(?string $path): void
    {
        if ($path === null || !str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
