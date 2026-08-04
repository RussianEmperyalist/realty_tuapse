<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\GalleryAlbum;
use App\Models\NewsPost;
use App\Models\Property;
use App\Support\OperationalMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $employeeId = $user?->isAdmin()
            ? (int) $request->session()->get('admin_employee_mode')
            : $user?->employee?->id;
        $employeeModeEmployee = null;

        if ($user?->isAdmin() && $employeeId > 0) {
            $employeeModeEmployee = Employee::query()->find($employeeId);
        }

        $propertiesQuery = Property::query()->with('employee')->orderByDesc('published_at');

        if ($employeeId) {
            $propertiesQuery->where('employee_id', $employeeId);
        }

        return view('admin.dashboard', [
            'user' => $user,
            'employeeFilter' => $employeeId,
            'isEmployeeModeActive' => $user?->isAdmin() && $employeeModeEmployee !== null,
            'employeeModeEmployee' => $employeeModeEmployee,
            'employees' => Employee::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'mailSummary' => app(OperationalMailService::class)->summary(),
            'properties' => $propertiesQuery->limit(10)->get(),
            'stats' => [
                'properties' => Property::query()->count(),
                'employees' => Employee::query()->count(),
                'news' => NewsPost::query()->count(),
                'gallery' => GalleryAlbum::query()->count(),
            ],
        ]);
    }

    /**
     * Set or reset the admin employee mode.
     */
    public function setEmployeeMode(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $employeeId = (int) $request->integer('employee_id');

        if ($employeeId > 0) {
            $request->session()->put('admin_employee_mode', $employeeId);
        } else {
            $request->session()->forget('admin_employee_mode');
        }

        return back()->with(
            'status',
            $employeeId > 0 ? 'Включен режим сотрудника.' : 'Режим сотрудника отключен.',
        );
    }

    /**
     * Send a live SMTP test email from the admin area.
     *
     * @throws ValidationException
     */
    public function sendMailTest(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        try {
            app(OperationalMailService::class)->sendTestMessage($validated['email']);

            return back()->with('status', 'Тестовое письмо отправлено. Проверьте указанный почтовый ящик.');
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'mail_test' => 'Не удалось отправить тестовое письмо: ' . Str::limit($exception->getMessage(), 300),
                ]);
        }
    }
}
