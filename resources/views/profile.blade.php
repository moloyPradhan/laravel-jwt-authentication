@extends('layouts.profile')

@section('title', 'Profile Page')

@section('content')

    <h1 class="text-2xl font-semibold mb-6">Your Profile</h1>

    <div class="space-y-6 max-w-xl">
        <div>
            <label for="name" class="block font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ $authUser['name'] ?? '' }}"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="email" class="block font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ $authUser['email'] ?? '' }}" readonly
                class="w-full border border-gray-300 bg-gray-100 rounded px-3 py-2 cursor-not-allowed" />
        </div>

        <div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold transition">
                Save Changes
            </button>
        </div>
    </div>

@endsection
