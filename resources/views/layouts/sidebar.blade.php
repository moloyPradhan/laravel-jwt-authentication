<div class="flex flex-1 max-w-7xl mx-auto px-4 py-8 space-x-6">
    <aside class="w-64 bg-white rounded shadow p-6 sticky top-16 h-[calc(100vh-4rem)]">
        <nav class="flex flex-col space-y-4">
            <a href="{{ route('profilePage') }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('profilePage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Profile
            </a>
            <a href="{{ route('profilePage') }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('loginPage*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Addresses
            </a>
            <a href="{{ route('orderPage') }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('orderPage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Orders
            </a>
            <a href="{{ route('userChatList') }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('userChatList*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Chats
            </a>
            <a href="{{ route('sellerDashboardPage') }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('seller*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Seller Dashboard
            </a>
        </nav>
    </aside>

    <main class="flex-1 bg-white rounded shadow p-6 min-h-[500px]">
        @yield('content')
    </main>
</div>
