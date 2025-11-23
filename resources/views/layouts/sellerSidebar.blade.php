<div class="flex flex-1 max-w-7xl mx-auto px-4 py-8 space-x-6">
    <aside class="w-64 bg-white rounded shadow p-6 sticky top-16 h-[calc(100vh-4rem)]">
        <nav class="flex flex-col space-y-4">

            <a href="{{ route('sellerRestaurantProfilePage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantProfilePage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Basic Details
            </a>
            <a href="{{ route('sellerRestaurantImagePage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantImagePage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Images
            </a>
            <a href="{{ route('sellerRestaurantDocumentPage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantDocumentPage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Documents
            </a>
            <a href="{{ route('sellerRestaurantAddressPage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantAddressPage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Address
            </a>
            <a href="{{ route('sellerRestaurantPage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantPage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Dashboard
            </a>
            <a href="{{ route('sellerRestaurantMenuPage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantMenuPage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Menu
            </a>
            <a href="{{ route('sellerRestaurantFoodPage', ['uid' => $restaurantId]) }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white {{ request()->routeIs('sellerRestaurantFoodPage') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Foods
            </a>
            <a href="{{ route('sellerDashboardPage') }}"
                class="block py-2 px-3 rounded hover:bg-blue-500 hover:text-white">
                Go Back
            </a>
        </nav>
    </aside>

    <main class="flex-1 bg-white rounded shadow p-6 min-h-[500px]">
        @yield('content')
    </main>
</div>
