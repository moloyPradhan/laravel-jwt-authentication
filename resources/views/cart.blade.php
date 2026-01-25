@extends('layouts.app')

@section('title', 'Cart')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@section('content')

    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <!-- ================= LEFT : DELIVERY ================= -->
        <div class="md:col-span-1">
            <div id="deliverySection" class="bg-white rounded-xl shadow p-4 space-y-4"></div>
        </div>

        <!-- ================= RIGHT : CART ================= -->
        <div class="md:col-span-2 space-y-4">

            <div id="restaurantInfo" class="bg-white rounded-xl shadow p-4 hidden">
                <h2 id="restaurantName" class="text-lg font-semibold"></h2>
            </div>

            <div id="cartItemsContainer" class="space-y-4 bg-white rounded-xl shadow p-4 min-h-[40vh] hidden"></div>

            <div id="cartSkeleton" class="bg-white p-4 rounded-xl shadow h-24 animate-pulse"></div>

            <div class="bg-white rounded-xl shadow p-4 sticky bottom-0">
                <div class="flex justify-between mb-3">
                    <p><span id="itemCount">0</span> items</p>
                    <p id="cartTotal" class="font-bold">₹0</p>
                </div>

                <button id="checkoutBtn"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold disabled:opacity-50" disabled>
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </div>

    <!-- ================= ADD ADDRESS MODAL ================= -->
    <div id="addressModal" class="fixed inset-0 bg-black/60 z-[9999] hidden flex items-center justify-center">

        <div class="bg-white rounded-xl w-[95%] max-w-md p-4 space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold">Add Delivery Address</h3>
                <button onclick="closeAddressModal()">✕</button>
            </div>

            <input id="addrLine" class="w-full border p-2 rounded" placeholder="Address line">

            <input id="addrLocality" class="w-full border p-2 rounded" placeholder="Locality">

            <input id="addrPincode" class="w-full border p-2 rounded" placeholder="Pincode">

            <div id="map" class="h-56 rounded"></div>

            <button onclick="saveDemoAddress()" class="w-full bg-green-600 text-white py-2 rounded">
                Save Address
            </button>
        </div>
    </div>

    <script type="module">
        import {
            httpRequest
        } from "/js/httpClient.js";

        const isLoggedIn = @json($isLoggedIn);

        let selectedAddressId = null;
        let hasCartItems = false;

        /* ================= DEMO ADDRESSES ================= */

        async function fetchAddresses() {

            const res = await httpRequest(`/api/users/addresses`);
            const addresses = res.data.addresses || [];

            if (addresses) {
                renderDelivery(addresses)
            }
        }

        fetchAddresses();

        /* ================= DELIVERY ================= */
        function renderDelivery(addresses) {

            const el = document.getElementById("deliverySection");

            if (!isLoggedIn) {
                el.innerHTML = `
                    <p class="text-gray-600">Login to add delivery location</p>
                    <a href="{{ route('loginPage') }}?source=cart"
                        class="block bg-gray-900 text-white text-center py-2 rounded">
                        Login
                    </a>`;
                return;
            }

            let html = `<h3 class="font-semibold">Delivery Addresses</h3>`;

            addresses.forEach(addr => {
                html += `
                    <label class="block border rounded p-3 cursor-pointer">
                        <input type="radio" name="address"
                            class="mr-2"
                            value="${addr.uid}"
                            ${addr.is_default ? "checked" : ""}>
                        <span class="font-medium">${addr.label}</span>
                        <p class="text-sm text-gray-600">
                            ${addr.address_line_1}, ${addr.city} - ${addr.postal_code}
                        </p>
                    </label>`;
            });

            html += `
                <button onclick="openAddressModal()"
                    class="w-full border py-2 rounded text-sm">
                    + Add New Address
                </button>`;

            el.innerHTML = html;

            document.querySelectorAll('input[name="address"]').forEach(radio => {
                radio.onchange = e => {
                    selectedAddressId = e.target.value;
                    updateCheckoutState();
                };
            });

            if (!selectedAddressId && addresses.length) {
                selectedAddressId = addresses[0].uid;
            }
        }

        /* ================= CART ================= */
        function cartItemRow(item) {
            const price = item.food.discount_price ?? item.food.price;
            return `
            <div class="flex justify-between border-b pb-2">
                <p>${item.food.name}</p>
                <p>₹${price * item.quantity}</p>
            </div>`;
        }

        async function fetchCart() {

            const res = await httpRequest(`/api/cart-items`);
            const items = res.data.items || [];
            const restaurant = res.data.restaurant;

            hasCartItems = items.length > 0;

            let html = "";
            let total = 0;

            items.forEach(i => {
                html += cartItemRow(i);
                total += (i.food.discount_price ?? i.food.price) * i.quantity;
            });

            cartItemsContainer.innerHTML =
                html || `<p class="text-center text-gray-500">Your cart is empty</p>`;

            itemCount.innerText = items.length;
            cartTotal.innerText = `₹${total}`;

            cartSkeleton.classList.add("hidden");
            cartItemsContainer.classList.remove("hidden");

            if (restaurant) {
                restaurantName.innerText = restaurant.name;


                restaurantInfo.classList.remove("hidden");
            }

            updateCheckoutState();
        }

        fetchCart();

        /* ================= CHECKOUT STATE ================= */
        function updateCheckoutState() {
            checkoutBtn.disabled = !(isLoggedIn && hasCartItems && selectedAddressId);
        }

        /* ================= MAP ================= */
        let map, marker, lat, lng;

        window.openAddressModal = () => {
            addressModal.classList.remove("hidden");
            setTimeout(initMap, 200);
        };

        window.closeAddressModal = () => {
            addressModal.classList.add("hidden");
        };

        function initMap() {

            if (map) return;

            navigator.geolocation.getCurrentPosition(pos => {

                lat = pos.coords.latitude;
                lng = pos.coords.longitude;

                map = L.map('map').setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                marker.on('dragend', e => {
                    lat = e.target.getLatLng().lat;
                    lng = e.target.getLatLng().lng;
                });
            });
        }

        /* ================= SAVE DEMO ADDRESS ================= */
        window.saveDemoAddress = () => {

            demoAddresses.push({
                id: Date.now(),
                address_line_1: addrLine.value,
                locality: addrLocality.value,
                city: "Demo City",
                pincode: addrPincode.value,
                lat,
                lng
            });

            closeAddressModal();
            renderDelivery(demoAddresses);
        };

        /* ================= CHECKOUT ================= */
        checkoutBtn.onclick = async () => {

            const res = await httpRequest(`/api/orders/create`, {
                method: "POST",
                body: {
                    address_id: selectedAddressId
                }
            });

            const {
                key,
                amount,
                currency,
                order_id
            } = res.data.payload;

            new Razorpay({
                key,
                amount,
                currency,
                order_id,
                handler: (response) => {

                    location.href = @json(route('homePage'))
                }
            }).open();
        };
    </script>

@endsection
