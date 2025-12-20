@extends('layouts.app')

@section('title', 'Restaurant Foods')

@section('content')

    <style>
        /* Floating Round Button */
        .sw-menu-btn {
            position: fixed;
            background: #111827;
            /* neutral-900 */
            color: #fff;
            border: none;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
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
            padding: 20px;
        }

        /* Menu Box */
        .sw-menu-box {
            width: 360px;
            max-height: 80vh;
            overflow: auto;
            background: #0B0910;
            color: white;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0px 8px 40px rgba(0, 0, 0, 0.6);
        }

        /* Menu List */
        .sw-menu-list div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 8px;
            font-size: 15px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sw-menu-list div:last-child {
            border-bottom: none;
        }

        .fixed-btn {
            position: fixed;
            right: 20px;
            z-index: 9999;
        }

        /* Cart top, menu below */
        #floatingCart {
            bottom: 110px !important;
        }

        #menuFloatBtn {
            bottom: 30px !important;
            right: 20px !important;
        }

        /* Slim rectangular card tweaks */
        .food-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 8px;
        }

        .food-card img {
            width: 96px;
            height: 72px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .food-card .meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .food-card .meta .title {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            line-height: 1.1;
        }

        .food-card .meta .price {
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
        }

        .food-card .meta .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 6px;
        }

        .add-btn-small {
            padding: 6px 10px;
            border-radius: 8px;
            background: #111827;
            color: #fff;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }

        .qty-box {
            display: inline-flex;
            align-items: center;
            gap: 18px;
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 600;
        }

        /* small responsive adjustments */
        @media (max-width: 640px) {
            .sw-menu-box {
                width: 100%;
                padding: 12px
            }

            .food-card img {
                width: 88px;
                height: 64px
            }
        }
    </style>

    <!-- Floating Cart Icon -->
    <a id="floatingCart" href="{{ route('cartItemsPage', ['uid' => $restaurantId]) }}"
        class="fixed-btn bg-gray-800 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center cursor-pointer hover:bg-gray-900 transition">
        <div class="relative flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437m0 0L6.75 14.25m-1.644-8.978h12.276c.938 0 1.636.88 1.42 1.792l-1.2 5.013a1.5 1.5 0 01-1.46 1.17H7.012m0 0L6.75 17.25m.262-2.988H18m-11.25 0A1.5 1.5 0 105.25 18a1.5 1.5 0 001.5-1.5zm10.5 0A1.5 1.5 0 1015.75 18a1.5 1.5 0 001.5-1.5z" />
            </svg>

            <span id="cartCount"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow">
                0
            </span>
        </div>
    </a>

    <button id="menuFloatBtn" class="sw-menu-btn fixed-btn">MENU</button>

    <!-- Swiggy Style Popup Menu -->
    <div id="swMenuPopup" class="sw-menu-popup">
        <div class="sw-menu-box">
            <div class="sw-menu-header flex justify-between items-center mb-3">
                <span class="text-lg font-semibold">Menu</span>
                <button id="swCloseMenu" class="text-white text-xl">✕</button>
            </div>
            <div id="swMenuList" class="sw-menu-list"></div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600">
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
                        class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-600 md:ml-2">Restaurants</a>
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
    <div id="menuFoodContainer" class="space-y-6">

        <!-- Skeleton Loader initially -->
        <div id="foodSkeleton" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
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

        const restaurantId = @json($restaurantId);

        let cart = {};

        function updateCartBadge() {
            // const total = Object.values(cart).reduce((a, b) => a + b, 0);
            const totalItems = Object.keys(cart).length;
            document.getElementById("cartCount").innerText = totalItems;
        }

        function generateFoodCard(food) {
            const mainImage = food.images?.find(img => img.image_type === "main");
            const imageUrl = mainImage?.image_url || "/images/placeholder.jpg";

            return `
            <div class="food-card" id="food_${food.id}">
                <img src="${imageUrl}" alt="${(food.name||'Food').replace(/"/g,'')}">

                <div class="meta">
                    <div>
                        <div class="title">${food.name}</div>
                        <div class="price">₹${"" /* placeholder for spacing */}
                            ${food.discount_price ? 
                                    `${food.discount_price}
                                                                <span class="line-through text-xs text-gray-400">${food.price}</span>` : `<span>${food.price}</span>`
                                }
                        </div>

                        ${food.preparation_time?`<span class="text-xs">Preparation Time : ${food.preparation_time} Min</span>`: ``}
                        
                    </div>

                    <div class="actions">
                        <div class="small-desc text-sm text-gray-500">${food.description || ''}</div>

                        <div class="action-controls" data-food-id="${food.uid}">
                            <button class="add-btn-small" data-food-id="${food.uid}">Add To Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        }

        function replaceWithQty(foodId) {
            const wrappers = document.querySelectorAll(
                `.action-controls[data-food-id="${foodId}"]`
            );

            if (!wrappers.length) return;

            wrappers.forEach(wrapper => {
                wrapper.innerHTML = `
                    <div class="qty-box" data-food-id="${foodId}">
                        <button class="qty-minus" data-food-id="${foodId}" aria-label="decrease">-</button>
                        <span class="qty-count">${cart[foodId] || 0}</span>
                        <button class="qty-plus" data-food-id="${foodId}" aria-label="increase">+</button>
                    </div>
                `;
            });
        }

        function updateButtonUI(foodId) {
            const wrappers = document.querySelectorAll(
                `.action-controls[data-food-id="${foodId}"]`
            );

            if (!wrappers.length) return;

            wrappers.forEach(wrapper => {
                if (cart[foodId]) {
                    replaceWithQty(foodId);
                } else {
                    wrapper.innerHTML = `
                        <button class="add-btn-small" data-food-id="${foodId}">
                            Add To Cart
                        </button>
                    `;
                }
            });

            updateCartBadge();
        }

        async function addOrUpdateCartItem(foodId) {
            try {
                const quantity = cart[foodId] || 0;
                const res = await httpRequest(`/api/restaurants/{{ $restaurantId }}/foods/${foodId}/cart`, {
                    method: "POST",
                    body: {
                        quantity
                    }
                });

                fetchCartItems();

            } catch (error) {
                console.log(error);
            }
        }


        function handleAddToCart(foodId) {
            cart[foodId] = (cart[foodId] || 0) + 1;
            updateButtonUI(foodId);
            addOrUpdateCartItem(foodId);
        }

        function handleQuantityChange(foodId, delta) {
            if (!cart[foodId]) return;
            cart[foodId] += delta;
            if (cart[foodId] <= 0) delete cart[foodId];
            updateButtonUI(foodId);
            addOrUpdateCartItem(foodId);

        }

        // Event delegation for add/qty buttons
        document.addEventListener("click", function(e) {
            const target = e.target;

            if (target.matches('.add-btn-small')) {
                const id = target.dataset.foodId;
                handleAddToCart(id);
                return;
            }

            if (target.matches('.qty-minus')) {
                const id = target.dataset.foodId;
                handleQuantityChange(id, -1);
                return;
            }

            if (target.matches('.qty-plus')) {
                const id = target.dataset.foodId;
                handleQuantityChange(id, 1);
                return;
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
                    (food.menus || []).forEach(m => {
                        if (menuFoodMap[m.uid]) menuFoodMap[m.uid].foods.push(food);
                    });
                });

                // Render UI sections
                let finalHtml = "";

                Object.values(menuFoodMap).forEach(group => {
                    if (group.foods.length === 0) return;

                    finalHtml += `
                        <div id="menu_${group.menu.uid}">
                            <h2 class="text-xl font-semibold text-gray-800 mb-3">${group.menu.name}</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                ${group.foods.map(f => generateFoodCard(f)).join("")}
                            </div>
                        </div>
                    `;
                });

                buildSwiggyMenuPopup(menus, menuFoodMap);
                document.getElementById("menuFoodContainer").innerHTML = finalHtml;

                fetchCartItems();

            } catch (err) {
                console.error("Error loading menu foods:", err);
            }
        }

        loadMenuFoods();

        async function fetchCartItems() {
            try {

                const res = await httpRequest(`/api/restaurants/{{ $restaurantId }}/cart-items`);
                const items = res?.data?.items || [];

                items.forEach((item, index) => {
                    cart[item.food_uid] = item.quantity;
                });

                Object.keys(cart).forEach(fid => updateButtonUI(fid));

            } catch (error) {
                console.log(error);
            }
        }

        function buildSwiggyMenuPopup(menus, menuFoodMap) {
            let html = "";

            menus.forEach(menu => {
                const count = menuFoodMap[menu.uid]?.foods?.length || 0;
                if (count > 0) {
                    html += `
                        <div data-scroll="menu_${menu.uid}" class="hover:bg-gray-800/20">
                            <span>${menu.name}</span>
                            <span class="text-sm text-gray-300">${count}</span>
                        </div>
                    `;
                }
            });

            document.getElementById("swMenuList").innerHTML = html;

            // Scroll action
            document.querySelectorAll("#swMenuList div").forEach(item => {
                item.addEventListener("click", () => {
                    const targetId = item.dataset.scroll;

                    // hide popup first
                    document.getElementById("swMenuPopup").style.display = "none";

                    // try to scroll to target if exists
                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        targetEl.scrollIntoView({
                            behavior: "smooth",
                            block: "start"
                        });
                    } else {
                        // fallback: try small timeout in case content still rendering
                        setTimeout(() => {
                            const t2 = document.getElementById(targetId);
                            if (t2) t2.scrollIntoView({
                                behavior: "smooth",
                                block: "start"
                            });
                        }, 120);
                    }
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
    </script>

@endsection
