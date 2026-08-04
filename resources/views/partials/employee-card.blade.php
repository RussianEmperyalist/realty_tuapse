@php
    $employeePhoto = \App\Support\MediaPath::url(
        $employee->photo_path,
        'legacy/themes/dolphin/assets/images/no_photo_entry.png',
    );
@endphp
<div class="card">
    <a href="{{ route('employees.show', ['id' => $employee->legacy_id]) }}"><img alt="{{ $employee->full_name }}" src="{{ $employeePhoto }}"></a>
    <h3>{{ $employee->full_name }}</h3>
    <p>{{ $employee->position }}</p>
    <div class="contact-info">
        <p><i class="fas fa-phone">&nbsp;</i> <a href="tel:{{ $employee->phone_primary }}">{{ $employee->phone_primary }}</a>@if($employee->phone_secondary), <a href="tel:{{ $employee->phone_secondary }}">{{ $employee->phone_secondary }}</a>@endif</p>
        @if ($employee->email)
            <p><i class="fas fa-envelope">&nbsp;</i> <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></p>
        @endif
    </div>
</div>
