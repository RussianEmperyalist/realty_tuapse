<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Support\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage,
    ) {
    }

    /**
     * List employees.
     */
    public function index(): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.employees.index', [
            'employees' => Employee::query()->with('user')->orderBy('sort_order')->paginate(20),
        ]);
    }

    /**
     * Show create employee form.
     */
    public function create(): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.employees.form', [
            'employee' => new Employee(['is_active' => true]),
            'formAction' => route('admin.employees.store'),
            'method' => 'post',
        ]);
    }

    /**
     * Store employee.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['full_name']);

        $user = $this->upsertUser(null, $request);
        $data['user_id'] = $user?->id;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->imageStorage->storePublicFile($request->file('photo'), 'employees');
        }

        Employee::query()->create($data);

        return redirect()
            ->route('admin.employees.index')
            ->with('status', 'Сотрудник создан.');
    }

    /**
     * Show employee edit form.
     */
    public function edit(Employee $employee): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $employee->load('user');

        return view('admin.employees.form', [
            'employee' => $employee,
            'formAction' => route('admin.employees.update', $employee),
            'method' => 'put',
        ]);
    }

    /**
     * Update employee.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $this->validatedData($request, $employee);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') ?: $data['full_name'], $employee->id);

        $user = $this->upsertUser($employee->user, $request);
        $data['user_id'] = $user?->id;

        if ($request->boolean('delete_photo')) {
            $this->deletePublicPath($employee->photo_path);
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            $this->deletePublicPath($employee->photo_path);
            $data['photo_path'] = $this->imageStorage->storePublicFile($request->file('photo'), 'employees');
        }

        $employee->update($data);

        return redirect()
            ->route('admin.employees.edit', $employee)
            ->with('status', 'Сотрудник обновлен.');
    }

    /**
     * Delete employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        if ($employee->user !== null) {
            $employee->user->delete();
        }

        $this->deletePublicPath($employee->photo_path);
        $employee->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('status', 'Сотрудник удален.');
    }

    /**
     * Validate employee fields.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Employee $employee = null): array
    {
        return $request->validate([
            'legacy_id' => ['nullable', 'integer', 'unique:employees,legacy_id' . ($employee ? ',' . $employee->id : '')],
            'full_name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:employees,slug' . ($employee ? ',' . $employee->id : '')],
            'position' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'phone_primary' => ['nullable', 'string', 'max:255'],
            'phone_secondary' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'bio' => ['nullable', 'string'],
            'is_admin' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'max:8192'],
            'login_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee?->user?->id),
            ],
            'login_password' => ['nullable', 'string', 'min:8'],
            'login_role' => ['nullable', 'in:admin,employee'],
        ]);
    }

    /**
     * Create or update the linked login.
     */
    private function upsertUser(?User $user, Request $request): ?User
    {
        $loginEmail = trim((string) $request->input('login_email'));
        if ($loginEmail === '') {
            return $user;
        }

        $data = [
            'name' => $request->input('full_name'),
            'email' => $loginEmail,
            'role' => $request->input('login_role', $request->boolean('is_admin') ? 'admin' : 'employee'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('login_password')) {
            $data['password'] = Hash::make((string) $request->input('login_password'));
        } elseif ($user === null) {
            $data['password'] = Hash::make((string) env('REALTY_DEMO_PASSWORD', 'RealtyDemo2026!'));
        }

        if ($user !== null) {
            $user->update($data);
            return $user;
        }

        return User::query()->create($data);
    }

    /**
     * Generate unique employee slug.
     */
    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug !== '' ? $baseSlug : 'employee';
        $counter = 1;

        while (Employee::query()->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Delete files from the public disk.
     */
    private function deletePublicPath(?string $path): void
    {
        if ($path === null || !str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
