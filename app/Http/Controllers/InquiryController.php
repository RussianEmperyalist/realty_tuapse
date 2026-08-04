<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\CallbackRequest;
use App\Models\ContactRequest;
use App\Models\Property;
use App\Models\PropertyMessage;
use App\Support\InquiryDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    /**
     * Show the contact form.
     */
    public function contactForm(): View
    {
        return view('forms.contact-us', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Связаться с нами'],
            ],
        ]);
    }

    /**
     * Store a contact request.
     */
    public function storeContact(Request $request, InquiryDeliveryService $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:4000'],
        ]);

        $contactRequest = ContactRequest::query()->create($validated + [
            'recipient_email' => config('realty.contact_email'),
            'delivery_status' => 'pending',
        ]);

        return $delivery->deliver(
            request: $request,
            inquiry: $contactRequest,
            subject: 'Заявка: Связаться с нами',
            headline: 'Новая заявка из формы "Связаться с нами"',
            lead: 'Сообщение отправлено с публичной страницы сайта.',
            fields: [
                ['label' => 'Имя', 'value' => $contactRequest->name],
                ['label' => 'Email', 'value' => $contactRequest->email],
                ['label' => 'Телефон', 'value' => $contactRequest->phone],
            ],
            messageBody: $contactRequest->message,
            successMessage: 'Сообщение отправлено. Мы свяжемся с вами в ближайшее время.',
        );
    }

    /**
     * Show the callback form.
     */
    public function callbackForm(): View
    {
        return view('forms.callback', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Заказать обратный звонок'],
            ],
        ]);
    }

    /**
     * Store a callback request.
     */
    public function storeCallback(Request $request, InquiryDeliveryService $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:4000'],
        ]);

        $callbackRequest = CallbackRequest::query()->create($validated + [
            'recipient_email' => config('realty.contact_email'),
            'delivery_status' => 'pending',
        ]);

        return $delivery->deliver(
            request: $request,
            inquiry: $callbackRequest,
            subject: 'Заявка: Обратный звонок',
            headline: 'Новый запрос на обратный звонок',
            lead: 'Клиент попросил связаться с ним по телефону.',
            fields: [
                ['label' => 'Имя', 'value' => $callbackRequest->name],
                ['label' => 'Телефон', 'value' => $callbackRequest->phone],
            ],
            messageBody: $callbackRequest->message,
            successMessage: 'Запрос принят. Мы перезвоним вам.',
        );
    }

    /**
     * Show the booking/request form.
     */
    public function bookingForm(): View
    {
        return view('forms.booking', [
            'bodyClass' => 'inner_page',
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Оставить заявку'],
            ],
        ]);
    }

    /**
     * Store a booking/request form.
     */
    public function storeBooking(Request $request, InquiryDeliveryService $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:4000'],
        ]);

        $bookingRequest = BookingRequest::query()->create($validated + [
            'recipient_email' => config('realty.contact_email'),
            'delivery_status' => 'pending',
        ]);

        return $delivery->deliver(
            request: $request,
            inquiry: $bookingRequest,
            subject: 'Заявка: Оставить заявку',
            headline: 'Новая заявка с сайта',
            lead: 'Клиент оставил общий запрос через публичную форму.',
            fields: [
                ['label' => 'Имя', 'value' => $bookingRequest->name],
                ['label' => 'Email', 'value' => $bookingRequest->email],
                ['label' => 'Телефон', 'value' => $bookingRequest->phone],
            ],
            messageBody: $bookingRequest->message,
            successMessage: 'Заявка отправлена. Мы свяжемся с вами.',
        );
    }

    /**
     * Show the property message form using the legacy query parameter.
     */
    public function propertyMessageForm(Request $request): View
    {
        $legacyId = (int) $request->integer('id');

        $property = Property::query()
            ->where('legacy_id', $legacyId)
            ->with('employee')
            ->firstOrFail();

        return view('forms.property-message', [
            'bodyClass' => 'inner_page',
            'property' => $property,
            'breadcrumbs' => [
                ['label' => 'Главная', 'url' => route('home')],
                ['label' => 'Сообщение по объекту'],
            ],
        ]);
    }

    /**
     * Store a message sent from the property page.
     */
    public function storePropertyMessage(Request $request, InquiryDeliveryService $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $property = Property::query()->findOrFail((int) $validated['property_id']);

        $propertyMessage = PropertyMessage::query()->create($validated + [
            'recipient_email' => config('realty.contact_email'),
            'delivery_status' => 'pending',
        ]);

        return $delivery->deliver(
            request: $request,
            inquiry: $propertyMessage,
            subject: 'Заявка по объекту: ' . $property->title,
            headline: 'Новая заявка по объекту',
            lead: 'Письмо отправлено из карточки недвижимости.',
            fields: [
                ['label' => 'Объект', 'value' => $property->title],
                ['label' => 'ID объекта', 'value' => (string) $property->legacy_id],
                ['label' => 'Ссылка', 'value' => route('properties.show', $property->slug)],
                ['label' => 'Имя', 'value' => $propertyMessage->name],
                ['label' => 'Email', 'value' => $propertyMessage->email],
                ['label' => 'Телефон', 'value' => $propertyMessage->phone],
            ],
            messageBody: $propertyMessage->message,
            successMessage: 'Сообщение по объекту отправлено.',
            successRedirect: redirect()->route('properties.show', $property->slug),
        );
    }
}
