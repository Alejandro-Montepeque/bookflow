@extends('errors.layout')

@section('title', 'Page expired')
@section('code', '419')
@section('heading', "This page expired")
@section('message', "Your session was inactive for too long. Please refresh and try again.")
@section('action', 'Refresh')

@section('icon')
    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 12a9 9 0 1115.66 6.05" />
        <path d="M21 21v-6h-6" />
    </svg>
@endsection
