<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Support\InquiryDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function create(): View
    {
        return view('auth.login', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Вход в личный кабинет'],
            ],
        ]);
    }

    /**
     * Attempt to authenticate the user.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'Не удалось войти. Проверьте email и пароль.',
                ]);
        }

        // Check if user account is active
        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();

            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'Учетная запись деактивирована. Обратитесь к администратору.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Log the user out.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Show the account request form.
     */
    public function registerForm(): View
    {
        return view('auth.register-request', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Регистрация'],
            ],
        ]);
    }

    /**
     * Store an access request for the agency.
     */
    public function storeRegisterRequest(Request $request, InquiryDeliveryService $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:4000'],
        ]);

        $accessRequest = AccessRequest::query()->create([
            'type' => 'register',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
            'recipient_email' => config('realty.contact_email'),
            'delivery_status' => 'pending',
        ]);

        return $delivery->deliver(
            request: $request,
            inquiry: $accessRequest,
            subject: 'Запрос доступа к личному кабинету',
            headline: 'Новая заявка на доступ к личному кабинету',
            lead: 'Сотрудник оставил заявку на создание или активацию учетной записи.',
            fields: [
                ['label' => 'Тип заявки', 'value' => 'Регистрация'],
                ['label' => 'Имя', 'value' => $accessRequest->name],
                ['label' => 'Email', 'value' => $accessRequest->email],
                ['label' => 'Телефон', 'value' => $accessRequest->phone],
            ],
            messageBody: $accessRequest->message,
            successMessage: 'Запрос на доступ отправлен. Администратор свяжется с вами.',
            successRedirect: redirect()->route('login'),
        );
    }

    /**
     * Show the password assistance form.
     */
    public function recoverForm(): View
    {
        return view('auth.recover-request', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Восстановление пароля'],
            ],
        ]);
    }

    /**
     * Store the password assistance request.
     */
    public function storeRecoverRequest(Request $request, InquiryDeliveryService $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:4000'],
        ]);

        $accessRequest = AccessRequest::query()->create([
            'type' => 'recover',
            'name' => null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
            'recipient_email' => config('realty.contact_email'),
            'delivery_status' => 'pending',
        ]);

        return $delivery->deliver(
            request: $request,
            inquiry: $accessRequest,
            subject: 'Запрос на восстановление доступа',
            headline: 'Новый запрос на восстановление доступа',
            lead: 'Пользователь сообщил, что потерял доступ к личному кабинету.',
            fields: [
                ['label' => 'Тип заявки', 'value' => 'Восстановление доступа'],
                ['label' => 'Email', 'value' => $accessRequest->email],
                ['label' => 'Телефон', 'value' => $accessRequest->phone],
            ],
            messageBody: $accessRequest->message,
            successMessage: 'Запрос на восстановление отправлен. Мы поможем вернуть доступ.',
            successRedirect: redirect()->route('login'),
        );
    }
}
