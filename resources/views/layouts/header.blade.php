<header class="bg-white shadow px-4 py-3 flex items-center justify-between">
    <div class="text-xl font-semibold">
        <a href="{{ url('/') }}">FulBite</a>
    </div>

    <div class="flex space-x-3 flex-1 max-w-3xl mx-6">
        <!-- Search input -->
        <input type="text" id="search" placeholder="Search..."
            class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none" />
    </div>

    <div class="relative" x-data="{ open: false }">
        @if ($isLoggedIn)
            <button @click="open = !open"
                class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 focus:outline-none">
                <p>{{ $authUser['name'] ?? 'User' }}</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50">
                <a href="{{ route('profilePage') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('profilePage') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('loginPage') }}" class="text-gray-700 hover:text-blue-600">Login</a>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</header>
