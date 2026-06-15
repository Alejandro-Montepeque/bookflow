@extends('errors.layout')

@section('title', 'Too many requests')
@section('code', '429')
@section('heading', "Slow down a little")
@section('message', "You've made too many requests in a short time. Please wait a moment before trying again.")

@section('icon')
    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10" />
        <path d="M12 6v6l4 2" />
    </svg>
@endsection
