@extends('layouts.app')

@section('title', 'Restaurant Foods Page')

@section('content')

    <style>
        /* Floating Round Button */
        .sw-menu-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: black;
            color: white;
            border: none;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            z-index: 9999;
        }

        /* Fullscreen Dim Background */
        .sw-menu-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        }

        /* Menu Box */
        .sw-menu-box {
            width: 350px;
            background: #0B0910;
            color: white;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0px 4px 30px rgba(0, 0, 0, 0.6);
        }

        /* Header */
        .sw-menu-header {
            display: flex;
            justify-content: space-between;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .sw-menu-header button {
            background: transparent;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        /* Menu List */
        .sw-menu-list div {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 17px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sw-menu-list div:last-child {
            border-bottom: none;
        }
    </style>

    <!-- Floating Cart Icon -->
    <div id="floatingCart"
        class="fixed bottom-24 right-6 bg-gray-800 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center cursor-pointer hover:bg-gray-900 transition">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437m0 0L6.75 14.25m-1.644-8.978h12.276c.938 0 1.636.88 1.42 1.792l-1.2 5.013a1.5 1.5 0 01-1.46 1.17H7.012m0 0L6.75 17.25m.262-2.988H18m-11.25 0A1.5 1.5 0 105.25 18a1.5 1.5 0 001.5-1.5zm10.5 0A1.5 1.5 0 1015.75 18a1.5 1.5 0 001.5-1.5z" />
            </svg>

            <span id="cartCount"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow">
                0
            </span>
        </div>
    </div>

    <button id="menuFloatBtn" class="sw-menu-btn">MENU</button>

    <!-- Swiggy Style Popup Menu -->
    <div id="swMenuPopup" class="sw-menu-popup">
        <div class="sw-menu-box">
            <div class="sw-menu-header">
                <span>Recommended</span>
                <button id="swCloseMenu">✕</button>
            </div>
            <div id="swMenuList" class="sw-menu-list"></div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                        </path>
                    </svg>
                    Home
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <a href="/restaurants"
                        class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Restaurants</a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Foods</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Food Section -->
    <div id="menuFoodContainer" class="space-y-10">

        <!-- Skeleton Loader initially -->
        <div id="foodSkeleton" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-4">
            @for ($i = 0; $i < 6; $i++)
                <div class="animate-pulse bg-white rounded-xl shadow overflow-hidden flex flex-col">
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

    </div>


    <script type="module">
        import {
            httpRequest
        } from "/js/httpClient.js";

        let cart = {};

        function updateCartBadge() {
            const total = Object.values(cart).reduce((a, b) => a + b, 0);
            document.getElementById("cartCount").innerText = total;
        }

        function generateFoodCard(food) {
            const mainImage = food.images?.find(img => img.image_type === "main");
            const imageUrl = mainImage?.image_url || "/images/placeholder.jpg";

            return `
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                <img src="${imageUrl}" class="h-40 w-full object-cover" />

                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="text-lg font-semibold text-gray-900">${food.name}</h3>

                    <div class="mt-2 text-gray-900 font-medium">
                        ${
                            food.discount_price
                                ? `
                                                    <span class="text-green-600 font-semibold">${food.discount_price} ${food.currency}</span>
                                                    <span class="line-through text-sm text-gray-400 ml-2">${food.price}</span>
                                                  `
                                : `<span>${food.price} ${food.currency}</span>`
                        }
                    </div>

                    <div class="mt-auto pt-3 cart-btn-wrapper" data-food-id="${food.id}">
                        <button class="add-to-cart px-4 py-2 w-full bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition"
                            data-food-id="${food.id}">Add to Cart</button>
                    </div>
                </div>
            </div>
        `;
        }

        function updateButtonUI(foodId) {
            const wrapper = document.querySelector(`.cart-btn-wrapper[data-food-id="${foodId}"]`);

            if (!wrapper) return;

            if (cart[foodId]) {
                wrapper.innerHTML = `
                <div class="flex items-center justify-between bg-gray-100 rounded-lg px-3 py-2">
                    <button class="minus-btn w-8 h-8 bg-gray-300 rounded hover:bg-gray-400 transition flex items-center justify-center"
                        data-food-id="${foodId}">-</button>

                    <span class="text-gray-800 font-semibold">${cart[foodId]}</span>

                    <button class="plus-btn w-8 h-8 bg-gray-300 rounded hover:bg-gray-400 transition flex items-center justify-center"
                        data-food-id="${foodId}">+</button>
                </div>`;
            } else {
                wrapper.innerHTML = `
                <button class="add-to-cart px-4 py-2 w-full bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition"
                    data-food-id="${foodId}">Add to Cart</button>
            `;
            }

            updateCartBadge();
        }

        function handleAddToCart(foodId) {
            cart[foodId] = (cart[foodId] || 0) + 1;
            updateButtonUI(foodId);
        }

        function handleQuantityChange(foodId, delta) {
            if (!cart[foodId]) return;

            cart[foodId] += delta;
            if (cart[foodId] <= 0) delete cart[foodId];

            updateButtonUI(foodId);
        }

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("add-to-cart")) {
                handleAddToCart(e.target.dataset.foodId);
            } else if (e.target.classList.contains("minus-btn")) {
                handleQuantityChange(e.target.dataset.foodId, -1);
            } else if (e.target.classList.contains("plus-btn")) {
                handleQuantityChange(e.target.dataset.foodId, 1);
            }
        });

        // Load menus + foods together
        async function loadMenuFoods() {
            try {
                const urlFoods = `/api/restaurants/{{ $restaurantId }}/foods`;
                const urlMenus = `/api/restaurants/{{ $restaurantId }}/menus`;

                const [foodsRes, menusRes] = await Promise.all([httpRequest(urlFoods), httpRequest(urlMenus)]);

                const foods = foodsRes?.data?.foods || [];
                const menus = menusRes?.data?.menus || [];

                // Build mapping menuUid → foods[]
                const menuFoodMap = {};
                menus.forEach(menu => {
                    menuFoodMap[menu.uid] = {
                        menu,
                        foods: []
                    };
                });

                foods.forEach(food => {
                    food.menus.forEach(m => {
                        if (menuFoodMap[m.uid]) {
                            menuFoodMap[m.uid].foods.push(food);
                        }
                    });
                });

                // Render UI
                let finalHtml = "";

                Object.values(menuFoodMap).forEach(group => {
                    if (group.foods.length === 0) return;

                    finalHtml += `
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">${group.menu.name}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            ${group.foods.map(f => generateFoodCard(f)).join("")}
                        </div>
                    </div>
                `;
                });

                document.getElementById("menuFoodContainer").innerHTML = finalHtml;

            } catch (err) {
                console.error("Error loading menu foods:", err);
            }
        }

        loadMenuFoods();


        function buildSwiggyMenuPopup(menus, menuFoodMap) {
            let html = "";

            menus.forEach(menu => {
                const count = menuFoodMap[menu.uid]?.foods?.length || 0;

                html += `
            <div data-scroll="menu_${menu.uid}">
                <span>${menu.name}</span>
                <span>${count}</span>
            </div>
        `;
            });

            document.getElementById("swMenuList").innerHTML = html;

            // Scroll action
            document.querySelectorAll("#swMenuList div").forEach(item => {
                item.addEventListener("click", () => {
                    const targetId = item.dataset.scroll;

                    document.getElementById("swMenuPopup").style.display = "none";

                    document.getElementById(targetId)
                        .scrollIntoView({
                            behavior: "smooth"
                        });
                });
            });
        }

        /* Popup open/close */
        document.getElementById("menuFloatBtn").onclick = () => {
            document.getElementById("swMenuPopup").style.display = "flex";
        };

        document.getElementById("swCloseMenu").onclick = () => {
            document.getElementById("swMenuPopup").style.display = "none";
        };

        /* Close when clicking outside the box */
        document.getElementById("swMenuPopup").onclick = (e) => {
            if (e.target.id === "swMenuPopup") {
                document.getElementById("swMenuPopup").style.display = "none";
            }
        };

        buildSwiggyMenuPopup(menus, menuFoodMap);
    </script>

@endsection
