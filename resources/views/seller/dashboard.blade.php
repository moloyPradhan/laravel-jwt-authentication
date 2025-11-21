@extends('layouts.app')

@section('title', 'Seller Dashboard')

@section('content')

    <!-- Dashboard STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow flex items-center p-6">
            <div class="flex-shrink-0 bg-blue-100 rounded-full p-4 mr-4">
                <!-- Check or badge icon SVG -->
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold" id="approvedStat">0</div>
                <div class="text-gray-500">Approved</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow flex items-center p-6">
            <div class="flex-shrink-0 bg-yellow-100 rounded-full p-4 mr-4">
                <!-- Clock icon SVG -->
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold" id="pendingStat">0</div>
                <div class="text-gray-500">Pending</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow flex items-center p-6">
            <div class="flex-shrink-0 bg-green-100 rounded-full p-4 mr-4">
                <!-- Check icon SVG -->
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold" id="activeStat">0</div>
                <div class="text-gray-500">Open</div>
            </div>
        </div>
    </div>

    <!-- RESTAURANT CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-4" id="restaurantList">
        <!-- Restaurants injected here by JS -->
    </div>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';


        const urlSellerRestaurant = @json(route('sellerAddRestaurantPage'));

        async function restaurants() {
            try {
                const url = `/api/users/restaurants`;
                const res = await httpRequest(url);
                const restaurants = res?.data?.restaurants || [];

                let html = "";

                if (restaurants.length === 0) {
                    html = `
                        <div class="col-span-full bg-white rounded-lg p-8 text-center text-gray-500">
                            You don't have any restaurants yet. 
                            <a href="${urlSellerRestaurant}" class="text-blue-600 hover:underline font-semibold ml-1">Click here to add</a>.
                        </div>
                        `;
                } else {
                    restaurants.forEach((item) => {
                        html += `
                            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                                <img src="${item?.images?.front_image || 'https://via.placeholder.com/400x160?text=No+Image'}" 
                                    alt="${item.name}" class="h-40 w-full object-cover" />
                                <div class="p-4 flex flex-col flex-grow">
                                    <div class="flex justify-between items-center mb-1">
                                        <h3 class="text-lg text-blue-500 font-semibold">
                                            <a href="${urlSellerRestaurant}/${item.uid}">${item.name}</a>
                                        </h3>
                                        <span class="px-2 py-1 rounded-full font-medium ${item.status === 'approved' ? 'bg-blue-100 text-blue-700' : item.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
                                            ${item.status}
                                        </span>
                                    </div>
                                    <p class="text-gray-500 text-sm mb-2">${item.description || ''}</p>
                                    <p class="text-gray-500 text-xs mb-3">
                                        ${item.addresses?.address_line_1 || ''}, ${item.addresses?.city || ''}, ${item.addresses?.postal_code || ''}
                                    </p>
                                </div>
                            </div>
                        `;
                    });

                    // Add extra card for adding a new restaurant
                    html += `
                        <div class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 text-gray-500 transition p-6">
                            <a href="${urlSellerRestaurant}" class="flex flex-col items-center space-y-2">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="font-medium text-lg">Add New Restaurant</span>
                            </a>
                        </div>
                    `;
                }

                document.getElementById('restaurantList').innerHTML = html;

            } catch (err) {
                console.log("Error :", err.message);
            }
        }

        restaurants();
    </script>

@endsection
