@extends('layouts.profile')

@section('title', 'Profile Page')

@section('content')

    <h1 class="text-2xl font-semibold mb-6">My Orders</h1>

    <div id="ordersContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl"></div>

    <!-- Modal -->
    <div id="orderModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-lg p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Order Details</h2>
                <button onclick="closeModal()" class="text-gray-500 text-xl">&times;</button>
            </div>

            <div id="modalContent" class="space-y-3"></div>
        </div>
    </div>


    <script type="module">
        import {
            httpRequest
        } from "/js/httpClient.js";

        const ordersContainer = document.getElementById('ordersContainer');
        const modal = document.getElementById('orderModal');
        const modalContent = document.getElementById('modalContent');

        async function fetchOrders() {
            try {
                const res = await httpRequest(`/api/orders`);
                renderOrders(res.data.orders);
            } catch (e) {
                console.error(e);
            }
        }

        function renderOrders(orders) {
            ordersContainer.innerHTML = '';

            orders.forEach(order => {
                const itemCount = order.order_items.length;

                const div = document.createElement('div');
                div.className =
                    "bg-white border rounded-xl p-4 shadow hover:shadow-md transition flex flex-col justify-between";

                div.innerHTML = `
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">#${order.uid}</p>
                                <p class="text-sm text-gray-500">
                                    ${new Date(order.created_at).toLocaleString()}
                                </p>
                            </div>

                            <span class="text-xs px-2 py-1 rounded ${statusBadge(order.status)}">
                                ${order.status}
                            </span>
                        </div>

                        <div class="mt-3 flex justify-between items-center">
                            <p class="text-sm text-gray-600">
                                ${itemCount} ${itemCount > 1 ? 'items' : 'item'}
                            </p>
                            <p class="font-bold">₹${order.amount}</p>
                        </div>
                    </div>

                    <button
                        class="mt-4 w-full text-sm font-medium text-blue-600 border border-blue-600 rounded-lg py-2 hover:bg-blue-50 transition view-details-btn">
                        View Details
                    </button>
                `;

                div.querySelector('.view-details-btn')
                    .addEventListener('click', () => openModal(order));

                ordersContainer.appendChild(div);
            });
        }


        function openModal(order) {
            modalContent.innerHTML = '';

            order.order_items.forEach(item => {
                modalContent.innerHTML += `
                    <div class="border rounded-lg p-3 flex justify-between">
                        <div>
                            <p class="font-medium">${item.food.name}</p>
                            <p class="text-sm text-gray-500">
                                Qty : ${item.quantity} × ₹${item.price}
                            </p>
                        </div>
                        <div class="font-semibold">
                            ₹${item.total}
                        </div>
                    </div>
                `;
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        window.closeModal = function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        fetchOrders();

        function statusBadge(status) {
            const map = {
                pending: 'bg-yellow-100 text-yellow-700',
                delivered: 'bg-green-100 text-green-700',
                cancelled: 'bg-red-100 text-red-700'
            };

            return map[status] ?? 'bg-gray-100 text-gray-700';
        }
    </script>


@endsection
