@extends('layouts.app')

@section('title', 'Cart')

@section('content')

    <!-- Breadcrumb -->
    <nav id="restaurantBreadcrumb" class="max-w-3xl mx-auto mb-3 hidden">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('homePage') }}" class="hover:text-gray-800 font-medium">Home</a>
            </li>
            <li>›</li>
            <li>
                <a href="{{ route('restautantPage') }}" class="hover:text-gray-800 font-medium">
                    Restaurant
                </a>
            </li>
            <li>›</li>
            <li>
                <a id="restaurantBackLink" href="#" class="hover:text-gray-800 font-medium">
                    Restaurant
                </a>
            </li>
            <li>›</li>
            <li class="text-gray-800">Cart</li>
        </ol>
    </nav>

    <!-- Restaurant Info -->
    <div id="restaurantInfo" class="max-w-3xl mx-auto mb-4 hidden">
        <div class="bg-white rounded-xl shadow p-4 space-y-1">
            <h2 id="restaurantName" class="text-lg font-semibold text-gray-800"></h2>
            <p id="restaurantAddress" class="text-sm text-gray-600"></p>
        </div>
    </div>

    <!-- Cart List -->
    <div class="max-w-3xl mx-auto">

        <!-- Skeleton -->
        <div id="cartSkeleton" class="space-y-4">
            @for ($i = 0; $i < 3; $i++)
                <div class="animate-pulse bg-white rounded-xl shadow p-4 flex justify-between">
                    <div class="space-y-2">
                        <div class="h-4 w-40 bg-gray-300 rounded"></div>
                        <div class="h-3 w-24 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-4 w-16 bg-gray-300 rounded"></div>
                </div>
            @endfor
        </div>

        <!-- Items -->
        <div id="cartItemsContainer" class="space-y-4 hidden min-h-[40vh] max-h-[40vh] overflow-y-auto pr-1">
        </div>
    </div>

    <!-- Sticky Footer -->
    <div class="sticky bottom-0 bg-white border-t mt-6">
        <div class="max-w-3xl mx-auto p-4 space-y-3">

            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">
                        <span id="itemCount">0</span> Items
                    </p>
                    <p class="text-lg font-semibold">Total</p>
                </div>
                <p id="cartTotal" class="text-xl font-bold">₹0</p>
            </div>

            @if ($isLoggedIn)
                <button id="checkoutBtn"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition disabled:opacity-50"
                    disabled>
                    Proceed to Checkout
                </button>
            @else
                <button onclick="window.location.href='{{ route('loginPage') }}?source=cart'"
                    class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-lg font-semibold transition">
                    Proceed to Login
                </button>
            @endif

        </div>
    </div>

    <!-- JS -->
    <script type="module">
        import {
            httpRequest
        } from "/js/httpClient.js";

        function cartItemRow(item) {
            const name = item.food?.name ?? "Food Item";
            const price = Number(item.food?.discount_price ?? item.food?.price ?? 0);
            const qty = Number(item.quantity ?? 0);
            const total = price * qty;

            return `
                <div class="flex items-center justify-between bg-white rounded-xl shadow-sm p-4">
                    <div>
                        <p class="font-semibold text-gray-800">${name} [${item?.food?.is_veg? "Veg":"Non-Veg"}]</p>
                        <p class="text-sm text-gray-500">₹${price} × ${qty}</p>
                        <p class="text-sm text-gray-500">Prepare Time : ${item?.food?.preparation_time} Min</p>
                        <p class="text-sm text-gray-500">${item?.food?.is_available?"": "Not Available"}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <p class="font-bold">₹${total}</p>
                        <button
                            data-cart-uid="${item.uid}"
                            class="remove-cart-item text-red-500 hover:text-red-700"
                            title="Remove">
                            🗑️
                        </button>
                    </div>
                </div>
            `;
        }

        let totalAmount = 0;
        async function fetchCartItems() {
            try {
                const res = await httpRequest(`/api/cart-items`);
                const restaurant = res?.data?.restaurant || null;
                const items = res?.data?.items || [];

                let html = "";

                let totalItems = 0;
                totalAmount = 0;

                if (restaurant) {
                    const address = restaurant.addresses?.[0];

                    document.getElementById("restaurantName").innerText = restaurant.name;
                    document.getElementById("restaurantBackLink").innerText = restaurant.name;

                    document.getElementById("restaurantAddress").innerText = address ?
                        `${address.address_line_1}, ${address.city}, ${address.postal_code}` :
                        restaurant.description || "";

                    document.getElementById("restaurantBackLink").href =
                        `/restaurants/${restaurant.uid}`;

                    document.getElementById("restaurantInfo").classList.remove("hidden");
                    document.getElementById("restaurantBreadcrumb").classList.remove("hidden");
                }

                items.forEach(item => {
                    const price = Number(item.food?.discount_price ?? item.food?.price ?? 0);
                    const qty = Number(item.quantity ?? 0);

                    html += cartItemRow(item);
                    totalAmount += price * qty;
                    totalItems++;
                });

                document.getElementById("cartSkeleton").classList.add("hidden");
                document.getElementById("cartItemsContainer").classList.remove("hidden");

                document.getElementById("cartItemsContainer").innerHTML =
                    html || `<p class="text-center text-gray-500 py-10">Your cart is empty</p>`;

                document.getElementById("cartTotal").innerText = `₹${totalAmount}`;
                document.getElementById("itemCount").innerText = totalItems;

                const checkoutBtn = document.getElementById("checkoutBtn");
                if (checkoutBtn) {
                    checkoutBtn.disabled = totalItems === 0;
                }

            } catch (e) {
                console.error(e);
            }
        }

        fetchCartItems();

        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".remove-cart-item");
            if (!btn) return;

            const cartItemId = btn.dataset.cartUid;
            await removeCartItem(cartItemId);
        });


        async function removeCartItem(cartItemId) {
            try {
                const res = await httpRequest(`/api/cart-items/${cartItemId}`, {
                    method: "DELETE",
                });

                fetchCartItems();

            } catch (error) {
                console.log(error);
            }

        }

        async function checkoutOrder() {
            var options = {
                "key": "rzp_test_LcrnvN0lkNSWgv",
                "amount": totalAmount,
                "currency": "INR",
                "name": "FulBite",
                "description": "Checkout Order",
                "order_id": "{{ $order_id }}",
                "handler": function(response) {
                    console.log(response);

                }
            };

            var rzp1 = new Razorpay(options);
            rzp1.open();

        }

        document.getElementById('checkoutBtn').addEventListener('click', () => {
            checkoutOrder();
        })
    </script>

@endsection
