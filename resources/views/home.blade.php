@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
    @if ($isLoggedIn)
        <p>Hello, {{ $authUser['name'] ?? 'User' }}!</p>
        {{-- <a href="{{ route('logout') }}">Logout</a> --}}
    @else
        <a href="{{ route('loginPage') }}">Login</a>
    @endif

    <a href="{{ route('userChatList') }}">Chats</a>

    @push('scripts')
        <script type="module">
            import {
                httpRequest
            } from '/js/httpClient.js';

            async function loadProfile() {
                try {
                    const data = await httpRequest("/api/auth/profile");
                    console.log(data);
                } catch (err) {
                    console.log("Failed to load profile:", err.message);
                }
            }

            loadProfile();
        </script>
    @endpush

@endsection
