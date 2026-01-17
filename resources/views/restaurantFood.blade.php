@extends('layouts.app')

@section('title', 'Restaurant Foods')

@section('content')

    <style>
        /* Hide scrollbar but keep scrolling */
        html,
        body {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE / Edge */
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari */
        }

        body.no-scroll {
            overflow: hidden;
        }

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
            max-height: 95vh;
            width: 500px;
            overflow: auto;
            background: #fff;
            color: #000;
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
            padding: 10px;
        }

        .food-card img {
            height: 100px;
            width: 100px;
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
            padding: 8px 8px;
            border-radius: 8px;
            background: #111827;
            color: #fff;
            font-size: 13px;
            border: none;
            cursor: pointer;
            width: max-content
        }

        .qty-box {
            display: inline-flex;
            align-items: center;
            gap: 22px;
            background: #f3f4f6;
            padding: 8px 8px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-disabled {
            background: #e5e7eb;
            color: #6b7280;
            cursor: not-allowed;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
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

        .food-detail-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 12px;
        }

        .food-detail-title {
            font-size: 18px;
            font-weight: 700;
        }

        .food-detail-price {
            font-size: 16px;
            font-weight: 600;
        }

        .food-detail-desc {
            font-size: 14px;
            color: #000;
        }

        /* ////////////// */

        /* Filter chips */
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            background: #f3f4f6;
            color: #111827;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .filter-chip.active {
            background: #111827;
            color: #fff;
        }

        .filter-chip.active .food-type {
            border-color: #fff;
        }

        .filter-chip.active .food-type::after {
            background: #fff;
        }

        /* Sort select */
        .sort-select {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            background: #fff;
        }

        /* ///////////////// */

        .food-type {
            width: 14px;
            height: 14px;
            border: 2px solid;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 3px;
        }

        .food-type::after {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        /* Veg */
        .food-type.veg {
            border-color: #16a34a;
            /* green */
        }

        .food-type.veg::after {
            background: #16a34a;
        }

        /* Non-Veg */
        .food-type.non-veg {
            border-color: #dc2626;
            /* red */
        }

        .food-type.non-veg::after {
            background: #dc2626;
        }
    </style>

    <!-- Floating Cart Icon -->
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

    <!-- Food Details Popup -->
    <div id="foodDetailPopup" class="sw-menu-popup">
        <div class="sw-menu-box max-w-md w-full">
            <div class="flex justify-between items-center mb-3">
                <span class="text-lg font-semibold">Food Details</span>
                <button id="closeFoodPopup" class="text-black text-xl">✕</button>
            </div>

            <div id="foodDetailContent"></div>
        </div>
    </div>

    <!-- Filter & Sort Bar -->


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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $restaurant->name }}</span>
                </div>
            </li>
        </ol>
    </nav>


    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

            <!-- Restaurant Info -->
            <div class="flex-1">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $restaurant->name }}
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $restaurant->description }}
                </p>

                <div class="text-sm text-gray-600 mt-3 space-y-1">
                    <p>📞 {{ $restaurant->phone }}</p>
                    <p>📧 {{ $restaurant->email }}</p>
                </div>
            </div>

            <!-- Address -->
            @if ($restaurant->addresses->isNotEmpty())
                @php $address = $restaurant->addresses->first(); @endphp

                <div class="md:max-w-sm text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <p class="leading-relaxed">
                        {{ $address->address_line_1 }},
                        {{ $address->address_line_2 }},
                        {{ $address->city }},
                        {{ $address->state }} - {{ $address->postal_code }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">

        <!-- Veg / Non-Veg Filter -->
        <button class="filter-chip active" data-filter="all">All</button>
        <button class="filter-chip" data-filter="veg">
            <span class="food-type veg"></span> Veg
        </button>
        <button class="filter-chip" data-filter="non-veg">
            <span class="food-type non-veg"></span> Non-Veg
        </button>

        <!-- Sort Dropdown -->
        <select id="sortSelect" class="sort-select ml-auto">
            <option value="">Sort by</option>
            <option value="price_low">Price: Low → High</option>
            <option value="price_high">Price: High → Low</option>
            <option value="time_low">Time: Fastest</option>
        </select>
    </div>



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
            <div class="food-card"id="food_${food.id}">
                <div class="flex gap-2 justify-content-between" onclick='openFoodPopup(${JSON.stringify(food).replace(/'/g,"&apos;")})'>
                    <img src="${imageUrl}" class="cursor-pointer"  alt="${(food.name||'Food').replace(/"/g,'')}">
                        <div class="meta">
                        <div class="title">
                            ${food.name} 
                        </div>
                        <div>
                            <div class="price">₹${"" /* placeholder for spacing */}
                                ${food.discount_price ? ` ${food.discount_price}  <span class="line-through text-xs text-gray-400"> ${food.price}</span>`
                                        :
                                        `<span>${food.price}</span>`
                                    }
                            </div>

                            <span class="text-xs">${food.is_veg? '<span class="food-type veg"></span>': '<span class="food-type non-veg"></span>'}
                            ${food.preparation_time?` • ${food.preparation_time} Min</span>`: ``}
                        </div>
                    </div>
                </div>
                <div class="actions ml-auto">
                    <div class="small-desc text-sm text-gray-500"></div>

                    ${food.is_available?`
                        <div class="action-controls" data-food-id="${food.uid}">
                            <button class="add-btn-small" data-food-id="${food.uid}">Add To Cart</button>
                        </div>`:
                    `<button class="btn-disabled" disabled>Not Available</button>`
                    }
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
                        <button class="qty-minus" data-food-id="${foodId}" aria-label="decrease"> 
                            -
                        </button>

                        <span class="qty-count">${cart[foodId] || 0}</span>

                        <button class="qty-plus" data-food-id="${foodId}" aria-label="increase">
                            +
                        </button>
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

        let allFoods = [];
        let allMenus = [];
        let menuFoodMap = {};
        let currentFilter = "all";
        let currentSort = "";
        let currentSearch = "";

        // Load menus + foods together
        async function loadMenuFoods() {
            try {
                const urlFoods = `/api/restaurants/{{ $restaurantId }}/foods`;
                const urlMenus = `/api/restaurants/{{ $restaurantId }}/menus`;

                const [foodsRes, menusRes] = await Promise.all([httpRequest(urlFoods), httpRequest(urlMenus)]);

                const foods = foodsRes?.data?.foods || [];
                const menus = menusRes?.data?.menus || [];

                allFoods = foods;
                allMenus = menus;

                // Build mapping menuUid → foods[]
                // const menuFoodMap = {};

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

                const res = await httpRequest(`/api/cart-items`);
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
                            <span class="text-sm text-gray-900">${count}</span>
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


        window.openFoodPopup = function(food) {
            const mainImage = food.images?.find(img => img.image_type === "main");
            const imageUrl = mainImage?.image_url || "/images/placeholder.jpg";

            document.getElementById("foodDetailContent").innerHTML = `
                <img src="${imageUrl}" class="food-detail-img mb-3">
                <div class="food-detail-title mb-1">${food.name}</div>

                <div class="flex justify-between items-center mb-2">
                    <div class="food-detail-price mb-2">
                        ₹${food.discount_price ?? food.price}  
                        ${food.discount_price ? `<span class="line-through text-sm text-gray-400"> ${food.price}</span>` : ``}
                    </div>

                    ${food.is_available ? `
                                                                <div class="action-controls" data-food-id="${food.uid}">
                                                                    ${cart[food.uid] ? `
                                    <div class="qty-box">
                                        <button class="qty-minus" data-food-id="${food.uid}">-</button>
                                        <span class="qty-count">${cart[food.uid]}</span>
                                        <button class="qty-plus" data-food-id="${food.uid}">+</button>
                                    </div>
                                ` : `
                                    <button class="add-btn-small" data-food-id="${food.uid}">
                                        Add To Cart
                                    </button>
                                `}
                                                                    </div>
                                                            ` : `<button class="btn-disabled w-full">Not Available</button>`}

                </div>

                <div class="text-sm mb-2">
                    ${food.is_veg ? `<span class="food-type veg"></span>` : `<span class="food-type non-veg"></span>`}
                    ${food.preparation_time ? ` • ${food.preparation_time} Min` : ``}
                </div>

                ${food.description ? `<p class="food-detail-desc mt-3">${food.description}</p>` : ``}
            `;

            document.getElementById("foodDetailPopup").style.display = "flex";

            // Disable background scroll
            document.body.classList.add("no-scroll");
        };

        document.getElementById("closeFoodPopup").onclick = () => {
            document.getElementById("foodDetailPopup").style.display = "none";
            document.body.classList.remove("no-scroll");
        };

        document.getElementById("foodDetailPopup").onclick = (e) => {
            if (e.target.id === "foodDetailPopup") {
                document.getElementById("foodDetailPopup").style.display = "none";
                document.body.classList.remove("no-scroll");
            }
        };

        //////////////////////////////////////////////////////////////////////////////

        function renderFoods() {
            let finalHtml = "";

            Object.values(menuFoodMap).forEach(group => {
                let foods = [...group.foods];

                if (currentSearch) {
                    foods = foods.filter(f =>
                        f.name.toLowerCase().includes(currentSearch)
                    );
                }


                // -------- FILTER --------
                if (currentFilter === "veg") {
                    foods = foods.filter(f => f.is_veg);
                }
                if (currentFilter === "non-veg") {
                    foods = foods.filter(f => !f.is_veg);
                }

                if (!foods.length) return;

                // -------- SORT --------
                if (currentSort === "price_low") {
                    foods.sort((a, b) =>
                        (a.discount_price ?? a.price) - (b.discount_price ?? b.price)
                    );
                }

                if (currentSort === "price_high") {
                    foods.sort((a, b) =>
                        (b.discount_price ?? b.price) - (a.discount_price ?? a.price)
                    );
                }

                if (currentSort === "time_low") {
                    foods.sort((a, b) =>
                        (a.preparation_time ?? 999) - (b.preparation_time ?? 999)
                    );
                }

                // -------- RENDER --------
                finalHtml += `
                    <div id="menu_${group.menu.uid}">
                        <h2 class="text-xl font-semibold text-gray-800 mb-3">
                            ${group.menu.name}
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            ${foods.map(f => generateFoodCard(f)).join("")}
                        </div>
                    </div>
                `;
            });

            document.getElementById("menuFoodContainer").innerHTML = finalHtml;
        }


        // Filter click
        document.querySelectorAll(".filter-chip").forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll(".filter-chip").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");

                currentFilter = btn.dataset.filter;
                renderFoods();
            };
        });

        // Sort change
        document.getElementById("sortSelect").onchange = (e) => {
            currentSort = e.target.value;
            renderFoods();
        };

        ///////////////////////////////////////////////////////////////////

        document.getElementById('search').addEventListener('input', (e) => {
            currentSearch = e.target.value.toLowerCase().trim();

            const params = new URLSearchParams(window.location.search);
            if (currentSearch) {
                params.set("search", currentSearch);
            } else {
                params.delete("search");
            }

            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.replaceState({}, "", newUrl);

            renderFoods();
        });
    </script>

@endsection
