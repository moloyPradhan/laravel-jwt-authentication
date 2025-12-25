@extends('layouts.app')

@section('title', 'Cart')

@section('content')

    <!-- Restaurant Info -->
    <div class="max-w-3xl mx-auto mb-4">
        <!-- Skeleton -->

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
        <div id="cartItemsContainer" class="space-y-4 hidden min-h-[50vh] max-h-[50vh] overflow-y-auto pr-1"></div>
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
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Proceed to Checkout
                </button>
            @else
                <button onclick="window.location.href='{{ route('loginPage') }}'"
                    class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-lg font-semibold transition">
                    Proceed to Login
                </button>
            @endif

        </div>
    </div>

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
                        <p class="font-semibold text-gray-800">${name}</p>
                        <p class="text-sm text-gray-500">₹${price} × ${qty}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <p class="font-bold">₹${total}</p>

                        <!-- Delete icon -->
                        <button 
                            onclick="removeCartItem('${item.food_uid}')"
                            class="text-red-500 hover:text-red-700 transition"
                            title="Remove item">
                            🗑️
                        </button>
                    </div>
                </div>
            `;
        }


        async function loadCartItems() {
            try {
                const res = await httpRequest(`/api/cart-items`);
                const items = res?.data?.items || [];

                let html = "";
                let totalAmount = 0;
                let totalItems = 0;

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

        loadCartItems();
    </script>

@endsection
