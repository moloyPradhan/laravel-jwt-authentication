@extends('layouts.seller')

@section('title', 'Seller Dashboard')

@section('content')
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

        <!-- Restaurant Basic Details Card -->
        <div class="bg-white shadow rounded-lg p-6 mb-8 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-10">
            <div>
                <div class="font-semibold text-lg mb-1" id="resto-name">The Biryani House</div>
                <div class="text-gray-600 mb-2 text-sm" id="resto-desc">Authentic Kolkata & Hyderabad biryanis, loved since
                    2010.</div>
                <div>
                    <span class="block text-sm text-gray-500 mb-1"><span class="font-medium">Email:</span> <span
                            id="resto-email">contact@biryanihouse.com</span></span>
                    <span class="block text-sm text-gray-500"><span class="font-medium">Mobile:</span> <span
                            id="resto-mobile">+91 90000 12345</span></span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-5 flex flex-col items-center">
                <div class="text-2xl font-semibold text-gray-800" id="stat-foods">12</div>
                <div class="text-gray-500 mt-1">Total Foods</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 flex flex-col items-center">
                <div class="text-2xl font-semibold text-gray-800" id="stat-orders">48</div>
                <div class="text-gray-500 mt-1">Total Orders</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 flex flex-col items-center">
                <div class="text-2xl font-semibold text-gray-800" id="stat-sales">₹15,200</div>
                <div class="text-gray-500 mt-1">Total Sales</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 flex flex-col items-center">
                <div class="flex items-center mb-1">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="onlineToggle" class="sr-only">
                        <div id="onlineToggleBg"
                            class="w-10 h-6 bg-gray-300 rounded-full transition-colors duration-200 relative">
                            <div id="onlineToggleDot"
                                class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200">
                            </div>
                        </div>
                    </label>
                </div>
                <div class="text-gray-500 mt-1" id="stat-online-status">Offline</div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-semibold">Recent Orders</h2>
            </div>
            <div id="recentOrders"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Restaurant Test Data
            const resto = {
                name: 'The Biryani House',
                description: 'Authentic Kolkata & Hyderabad biryanis, loved since 2010.',
                email: 'contact@biryanihouse.com',
                mobile: '+91 90000 12345'
            };
            document.getElementById('resto-name').innerText = resto.name;
            document.getElementById('resto-desc').innerText = resto.description;
            document.getElementById('resto-email').innerText = resto.email;
            document.getElementById('resto-mobile').innerText = resto.mobile;

            // Sample online status
            let isOnline = false;
            const onlineToggle = document.getElementById('onlineToggle');
            const onlineToggleBg = document.getElementById('onlineToggleBg');
            const onlineToggleDot = document.getElementById('onlineToggleDot');
            const statOnlineStatus = document.getElementById('stat-online-status');

            function setToggleUI(isOnline) {
                if (isOnline) {
                    onlineToggleBg.classList.add('bg-green-400');
                    onlineToggleBg.classList.remove('bg-gray-300');
                    onlineToggleDot.classList.add('translate-x-4');
                    statOnlineStatus.innerText = "Online";
                    statOnlineStatus.classList.add('text-green-600');
                    statOnlineStatus.classList.remove('text-gray-400');
                } else {
                    onlineToggleBg.classList.remove('bg-green-400');
                    onlineToggleBg.classList.add('bg-gray-300');
                    onlineToggleDot.classList.remove('translate-x-4');
                    statOnlineStatus.innerText = "Offline";
                    statOnlineStatus.classList.remove('text-green-600');
                    statOnlineStatus.classList.add('text-gray-400');
                }
            }
            onlineToggle.checked = isOnline;
            setToggleUI(isOnline);
            onlineToggle.addEventListener('change', function() {
                isOnline = onlineToggle.checked;
                setToggleUI(isOnline);
            });

            // Test Data for Recent Orders
            const orders = [{
                    order_number: 'A201',
                    customer_name: 'Riya Sen',
                    created_at: '2025-11-23 18:40',
                    amount: 340,
                    status: 'delivered'
                },
                {
                    order_number: 'A200',
                    customer_name: 'Anil Das',
                    created_at: '2025-11-23 18:30',
                    amount: 560,
                    status: 'pending'
                },
                {
                    order_number: 'A199',
                    customer_name: 'Lata Pal',
                    created_at: '2025-11-23 17:40',
                    amount: 110,
                    status: 'delivered'
                }
            ];
            const ordersHtml = orders.map(order => `
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span>
                <span class="font-medium">#${order.order_number}</span>
                <span class="ml-2">${order.customer_name}</span>
                <span class="ml-2 text-gray-500">${order.created_at}</span>
            </span>
            <span class="font-semibold">₹${order.amount}</span>
            <span class="px-2 py-0.5 rounded text-xs uppercase 
                ${order.status === 'delivered' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-700'}">
                    ${order.status}
            </span>
        </div>
    `).join('');
            document.getElementById('recentOrders').innerHTML = ordersHtml;
        });
    </script>
@endsection
