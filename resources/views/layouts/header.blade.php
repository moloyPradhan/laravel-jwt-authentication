<header class="bg-white shadow px-4 py-3 flex items-center justify-between">
    <div class="text-xl font-semibold">
        <a href="{{ url('/') }}">FulBite</a>
    </div>

    <div class="flex space-x-3 flex-1 max-w-3xl mx-6">
        <!-- Search input -->
        <input type="text" id="search" placeholder="Search..."
            class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <!-- Pincode autocomplete input -->
        <input type="text" id="pincode" placeholder="Enter Pincode"
            class="w-32 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
    </div>

    <div class="flex space-x-4 items-center">
        @if ($isLoggedIn)
            <p>Hello, {{ $authUser['name'] ?? 'User' }}!</p>
            {{-- <a href="{{ route('profile') }}" class="text-gray-700 hover:text-blue-600">Profile</a> --}}
            <button type="submit" class="text-gray-700 hover:text-red-600">Logout</button>
        @else
            <a href="{{ route('loginPage') }}" class="text-gray-700 hover:text-blue-600">Login</a>
        @endif




    </div>
</header>
