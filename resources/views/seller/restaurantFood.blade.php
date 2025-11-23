@extends('layouts.seller')

@section('title', 'Restaurant Foods')

@section('content')

    <div class="space-y-5 max-w-xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <div class="text-xl font-bold">Foods</div>
            <button id="btnAdd" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add New</button>
        </div>
        <div id="foodContainer" class="space-y-5 max-w-xl"></div>

    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        async function fetchMenuItems() {
            const res = await httpRequest('/api/restaurants/{{ $restaurantId }}/foods');
            const foods = res?.data?.foods || [];
            renderFoods(foods);
        }

        function renderFoods(foodList) {
            const container = document.getElementById('foodContainer');
            container.innerHTML = '';
            foodList.forEach(food => {

                const imageUrl = food.images && food.images.length > 0 ?
                    food.images[0].image_url :
                    'https://via.placeholder.com/80x80?text=No+Image';

                const vegText = food.is_veg ? 'Veg' : 'Non-Veg';
                const vegColor = food.is_veg ? 'text-green-600 border-green-400' : 'text-red-600 border-red-400';

                const availText = food.is_available ? 'Available' : 'Unavailable';
                const availColor = food.is_available ? 'bg-green-100 text-green-700' : 'bg-gray-300 text-gray-500';

                const priceHtml = food.discount_price ?
                    `<div>
                        <span class="text-gray-400 line-through mr-2">₹${food.price}</span>
                        <span class="text-green-700 font-bold">₹${food.discount_price}</span>
                    </div>` :
                    `<div class="text-gray-900 font-semibold">₹${food.price}</div>`;

                const tagsHtml = (food.tags || [])
                    .map(tag =>
                        `<span class="border px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-800 mr-1">${tag}</span>`
                        )
                    .join('');


                const card = document.createElement('div');
                card.className = 'bg-white shadow-md rounded-xl p-4 flex items-center space-x-4';

                card.innerHTML = `
                    <img src="${imageUrl}" alt="${food.name}" class="w-20 h-20 object-cover rounded" />
                    <div class="flex-1">
                        <div class="flex items-center md:space-x-3 space-y-1 md:space-y-0 flex-col md:flex-row">
                            <div class="text-lg font-bold">${food.name}</div>
                        </div>
                        ${priceHtml}
                        <div class="text-sm text-gray-500 my-1">Preparation : ${food.preparation_time} min</div>
                        <div class="my-1">
                            <span class="border px-2 py-0.5 rounded text-xs ${vegColor}">${vegText}</span>
                            <span class="rounded px-2 py-0.5 ${availColor} text-xs">${availText}</span>
                        </div>
                        <div class="my-1">
                            ${tagsHtml}
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        fetchMenuItems()
    </script>


@endsection
