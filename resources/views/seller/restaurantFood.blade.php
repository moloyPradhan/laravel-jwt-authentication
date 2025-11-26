@extends('layouts.seller')

@section('title', 'Restaurant Foods')

@section('content')
    <div class="space-y-5 max-w-xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <div class="text-xl font-bold">Foods</div>
            <a href="{{ route('sellerRestaurantAddFoodPage', ['uid' => $restaurantId]) }}" id="btnAdd"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add New</a>
        </div>
        <div id="foodContainer" class="space-y-5"></div>
    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        const container = document.getElementById('foodContainer');
        const currentUrl = @json(route('sellerRestaurantFoodPage', ['uid' => $restaurantId]));

        async function fetchMenuItems() {
            const res = await httpRequest('/api/restaurants/{{ $restaurantId }}/foods');
            const foods = res?.data?.foods || [];

            if (foods.length == 0) {
                container.innerHTML = `<p class="text-center">No item available</p>`
                return
            }

            renderFoods(foods);
        }

        function renderFoods(foodList) {
            container.innerHTML = '';
            foodList.forEach(food => {
                const imageUrl = food.images && food.images.length > 0 ?
                    food.images[0].image_url :
                    'https://via.placeholder.com/80x80?text=No+Image';

                const vegText = food.is_veg ? 'Veg' : 'Non-Veg';
                const vegColor = food.is_veg ? 'text-green-600 border-green-400' : 'text-red-600 border-red-400';

                const availText = food.is_available ? 'Available' : 'Unavailable';
                const availColor = food.is_available ? 'text-green-600' : 'text-gray-400';

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
                card.className = 'bg-white shadow rounded-xl px-6 py-4 flex space-x-4 items-center relative';

                card.innerHTML = `
                    <div class="flex flex-col items-center justify-between w-24 min-w-24">
                        <img src="${imageUrl}" alt="${food.name}" class="w-20 h-20 object-cover rounded-lg border mb-2" />
                        <label class="inline-flex items-center cursor-pointer mt-1">
                            <input type="checkbox" class="availability-toggle sr-only" data-id="${food.uid}" ${food.is_available ? 'checked' : ''}>
                            <div class="relative">
                                <div class="switch-bg block w-10 h-6 rounded-full transition-colors duration-200 ${food.is_available ? 'bg-green-400' : 'bg-gray-300'}"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 ${food.is_available ? 'translate-x-4' : ''}"></div>
                            </div>
                        </label>
                        <span class="text-xs mt-1 ${availColor}">${availText}</span>
                    </div>
                    <div class="flex-1 flex flex-col pl-2">
                        <div class="flex justify-between w-full items-baseline">
                            <span class="text-lg font-bold leading-tight">
                                <a href="${currentUrl}/${food.uid}/images">
                                    ${food.name}
                                </a>
                            </span>
                            <div>
                                ${priceHtml}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="border px-2 py-0.5 rounded text-xs ${vegColor}">${vegText}</span>
                            <span class="text-gray-500 text-xs">Preparation : ${food.preparation_time} min</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            ${tagsHtml}
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });

            // Attach toggle listeners after rendering
            container.querySelectorAll(".availability-toggle").forEach(toggle => {
                toggle.addEventListener("change", async (e) => {
                    const foodId = e.target.getAttribute("data-id");
                    const isChecked = e.target.checked ? 1 : 0;

                    const dot = e.target.nextElementSibling.querySelector('.dot');
                    if (dot) dot.classList.toggle("translate-x-4", isChecked);

                    if (isChecked) {
                        dot.classList.removeClass("bg-gray-300");
                        dot.classList.addClass("bg-green-400");
                    } else {
                        dot.classList.removeClass("bg-green-400");
                        dot.classList.addClass("bg-gray-300");
                    }

                    // try {
                    //     await httpRequest(`/api/foods/${foodId}/availability`, {
                    //         method: "PATCH",
                    //         body: {
                    //             is_available: isChecked
                    //         }
                    //     });
                    //     showToast("success", isChecked ? "Marked available" : "Marked unavailable");
                    //     // Move the blue dot visually (for older Tailwind, you may want to use JS to animate .dot)
                    //     const dot = e.target.nextElementSibling.querySelector('.dot');
                    //     if (dot) dot.classList.toggle("translate-x-4", isChecked);
                    // } catch (error) {
                    //     showToast("error", "Failed to update availability");
                    //     e.target.checked = !isChecked; // rollback UI in case of failure
                    // }
                });
            });
        }

        fetchMenuItems();
    </script>
@endsection
