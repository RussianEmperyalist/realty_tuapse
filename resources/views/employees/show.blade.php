@extends('layouts.site')

@section('title', $employee->full_name)

@section('content')
    <div class="content_box content">
        <div class="box">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <img src="{{ \App\Support\MediaPath::url($employee->photo_path, 'legacy/themes/dolphin/assets/images/no_photo_entry.png') }}" alt="{{ $employee->full_name }}" style="width: 100%; max-height: 420px; object-fit: cover; border-radius: 8px;">
                </div>
                <div class="col-md-8 col-sm-12">
                    <h1 class="fint l_fint">{{ $employee->full_name }}</h1>
                    <p><strong>{{ $employee->position }}</strong></p>
                    <ul class="list-unstyled">
                        @if ($employee->phone_primary)
                            <li><i class="fas fa-phone"></i> <a href="tel:{{ $employee->phone_primary }}">{{ $employee->phone_primary }}</a></li>
                        @endif
                        @if ($employee->phone_secondary)
                            <li><i class="fas fa-phone"></i> <a href="tel:{{ $employee->phone_secondary }}">{{ $employee->phone_secondary }}</a></li>
                        @endif
                        @if ($employee->email)
                            <li><i class="fas fa-envelope"></i> <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="box" style="margin-top: 30px;">
            <div class="h3 fint l_fint">Объявления сотрудника</div>
            <div class="catalog">
                <div class="row">
                    @forelse ($properties as $property)
                        @include('partials.property-card', ['property' => $property])
                    @empty
                        <div class="col-md-12">
                            <p>У сотрудника пока нет опубликованных объектов.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
