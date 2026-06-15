@extends('errors.layout')

@section('title', 'Something went wrong')
@section('code', '500')
@section('heading', "Sorry — we hit a bump")
@section('message', "Our team has been notified. Try refreshing in a moment, or head back home.")

@section('icon')
    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 9v3M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
    </svg>
@endsection
