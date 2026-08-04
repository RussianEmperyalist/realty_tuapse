<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display all active employees.
     */
    public function index(): View
    {
        $isContactsPage = request()->routeIs('contacts');

        return view('employees.index', [
            'bodyClass' => 'inner_page',
            'employees' => Employee::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => $isContactsPage ? 'Контакты' : 'Сотрудники'],
            ],
        ]);
    }

    /**
     * Display one employee using the legacy query-based route.
     */
    public function show(Request $request): View
    {
        $legacyId = (int) $request->integer('id');

        $employee = Employee::query()
            ->where('legacy_id', $legacyId)
            ->where('is_active', true)
            ->firstOrFail();

        $properties = Property::query()
            ->with(['employee', 'images'])
            ->where('employee_id', $employee->id)
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->get();

        return view('employees.show', [
            'bodyClass' => 'inner_page',
            'employee' => $employee,
            'properties' => $properties,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Сотрудники', 'url' => route('employees.index')],
                ['label' => $employee->full_name],
            ],
        ]);
    }
}
