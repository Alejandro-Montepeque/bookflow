@extends('errors.layout')

@section('title', 'Page not found')
@section('code', '404')
@section('heading', "We couldn't find that page")
@section('message', "The link might be broken, or the page may have been moved. Let's get you back to safety.")

@section('icon')
    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8" />
        <path d="M21 21l-4.35-4.35" />
    </svg>
@endsection
