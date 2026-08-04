<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\BookingRequest;
use App\Models\CallbackRequest;
use App\Models\ContactRequest;
use App\Models\PropertyMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class InquiryController extends Controller
{
    /**
     * Display a combined inquiry journal for administrators.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $typeFilter = (string) $request->query('type', '');

        $items = $this->collectInquiries()
            ->when($typeFilter !== '', fn (Collection $collection) => $collection->where('type', $typeFilter))
            ->sortByDesc('created_at')
            ->values();

        return view('admin.inquiries.index', [
            'items' => $items,
            'typeFilter' => $typeFilter,
            'typeOptions' => [
                'contact' => 'Связаться с нами',
                'callback' => 'Обратный звонок',
                'booking' => 'Оставить заявку',
                'property_message' => 'Сообщение по объекту',
                'register' => 'Регистрация',
                'recover' => 'Восстановление доступа',
            ],
        ]);
    }

    /**
     * Build a unified collection from all inquiry sources.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function collectInquiries(): Collection
    {
        $contacts = ContactRequest::query()->latest()->get()->map(fn (ContactRequest $item) => [
            'type' => 'contact',
            'type_label' => 'Связаться с нами',
            'name' => $item->name,
            'email' => $item->email,
            'phone' => $item->phone,
            'message' => $item->message,
            'recipient_email' => $item->recipient_email,
            'delivery_status' => $item->delivery_status ?? 'pending',
            'delivery_error' => $item->delivery_error,
            'sent_at' => $item->sent_at,
            'created_at' => $item->created_at,
            'context' => null,
        ]);

        $callbacks = CallbackRequest::query()->latest()->get()->map(fn (CallbackRequest $item) => [
            'type' => 'callback',
            'type_label' => 'Обратный звонок',
            'name' => $item->name,
            'email' => null,
            'phone' => $item->phone,
            'message' => $item->message,
            'recipient_email' => $item->recipient_email,
            'delivery_status' => $item->delivery_status ?? 'pending',
            'delivery_error' => $item->delivery_error,
            'sent_at' => $item->sent_at,
            'created_at' => $item->created_at,
            'context' => null,
        ]);

        $bookings = BookingRequest::query()->with('property')->latest()->get()->map(fn (BookingRequest $item) => [
            'type' => 'booking',
            'type_label' => 'Оставить заявку',
            'name' => $item->name,
            'email' => $item->email,
            'phone' => $item->phone,
            'message' => $item->message,
            'recipient_email' => $item->recipient_email,
            'delivery_status' => $item->delivery_status ?? 'pending',
            'delivery_error' => $item->delivery_error,
            'sent_at' => $item->sent_at,
            'created_at' => $item->created_at,
            'context' => $item->property?->title,
        ]);

        $propertyMessages = PropertyMessage::query()->with('property')->latest()->get()->map(fn (PropertyMessage $item) => [
            'type' => 'property_message',
            'type_label' => 'Сообщение по объекту',
            'name' => $item->name,
            'email' => $item->email,
            'phone' => $item->phone,
            'message' => $item->message,
            'recipient_email' => $item->recipient_email,
            'delivery_status' => $item->delivery_status ?? 'pending',
            'delivery_error' => $item->delivery_error,
            'sent_at' => $item->sent_at,
            'created_at' => $item->created_at,
            'context' => $item->property?->title,
        ]);

        $accessRequests = AccessRequest::query()->latest()->get()->map(fn (AccessRequest $item) => [
            'type' => $item->type,
            'type_label' => $item->type === 'recover' ? 'Восстановление доступа' : 'Регистрация',
            'name' => $item->name,
            'email' => $item->email,
            'phone' => $item->phone,
            'message' => $item->message,
            'recipient_email' => $item->recipient_email,
            'delivery_status' => $item->delivery_status ?? 'pending',
            'delivery_error' => $item->delivery_error,
            'sent_at' => $item->sent_at,
            'created_at' => $item->created_at,
            'context' => null,
        ]);

        return collect()
            ->concat($contacts)
            ->concat($callbacks)
            ->concat($bookings)
            ->concat($propertyMessages)
            ->concat($accessRequests);
    }
}
