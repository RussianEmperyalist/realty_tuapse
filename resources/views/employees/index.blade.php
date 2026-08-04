@extends('layouts.site')

@section('title', request()->routeIs('contacts') ? 'Контакты' : 'Сотрудники')

@push('styles')
    <style type="text/css">
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 15px;
            width: calc(33.33% - 20px);
            box-sizing: border-box;
        }
        .card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }
        .card h3 {
            font-size: 20px;
            margin: 10px 0;
        }
        .card p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .contact-info {
            margin-top: 10px;
        }
        .contact-info p {
            margin: 2px 0;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .card {
                width: calc(100% - 20px);
            }
        }
    </style>
@endpush

@section('content')
    <div class="content_box content">
        <h1 class="fint l_fint">{{ request()->routeIs('contacts') ? 'Контакты' : 'Сотрудники' }}</h1>
        <div class="container">
            @foreach ($employees as $employee)
                @include('partials.employee-card', ['employee' => $employee])
            @endforeach
        </div>
    </div>
@endsection
