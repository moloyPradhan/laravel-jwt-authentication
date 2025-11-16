@extends('layouts.app')

@section('title', 'Home Page')

@section('content')

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-4" id="restaurantList">

        @for ($i = 0; $i < 6; $i++)
            <div class="animate-pulse bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                <div class="h-40 bg-gray-300 w-full"></div>
                <div class="p-4 flex flex-col flex-grow space-y-3">
                    <div class="h-6 bg-gray-300 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-300 rounded w-full"></div>
                    <div class="h-4 bg-gray-300 rounded w-5/6"></div>
                    <div class="h-8 bg-gray-300 rounded w-24 mt-auto"></div>
                </div>
            </div>
        @endfor

    </div>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';


        async function restaurants() {
            try {
                const url = `/api/restaurants`;
                const res = await httpRequest(url);
                const restaurants = res?.data?.restaurants || [];

                let html = "";

                restaurants.forEach((item) => {
                    html += `
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                        <img src="${item.images?.front_image }" alt="${item.name}"
                            class="h-40 w-full object-cover" />
                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="text-lg font-semibold mb-1">${item.name}</h3>
                            <p class="text-gray-500 text-sm mb-3">${item.description}</p>
                            <p class="text-gray-500 text-sm mb-3">
                                ${item.addresses?.address_line_1}, ${item.addresses?.city}, ${item.addresses?.postal_code}
                            </p>
                            <a href="#" class="mt-auto inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8H6a2 2 0 01-2-2V6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2z" />
                                </svg>
                                Chat
                            </a>
                        </div>
                    </div>
                    `
                });

                document.getElementById('restaurantList').innerHTML = html

            } catch (err) {
                console.log("Error :", err.message);
            }
        }

        restaurants();
    </script>

@endsection
