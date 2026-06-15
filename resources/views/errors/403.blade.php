@extends('errors.layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('heading', "You don't have access to this resource")
@section('message', "If you think this is a mistake, contact the workspace owner.")

@section('icon')
    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2" />
        <path d="M7 11V7a5 5 0 0110 0v4" />
    </svg>
@endsection
