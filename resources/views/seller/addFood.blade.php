@extends('layouts.seller')

@section('title', 'Add Food')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6 mt-8">
        <h2 class="text-2xl font-bold mb-6">Add New Food Item</h2>
        <form id="addFoodForm" class="space-y-6">
            <div>
                <label class="block mb-1 text-sm font-semibold">Name</label>
                <input type="text" name="name"
                    class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    required>
            </div>
            <div>
                <label class="block mb-1 text-sm font-semibold">Description</label>
                <textarea name="description"
                    class="block w-full border border-gray-300 rounded px-3 py-2 resize-none focus:ring-green-500 focus:border-green-500"
                    rows="2"></textarea>
            </div>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block mb-1 text-sm font-semibold">Price (₹)</label>
                    <input type="number" name="price"
                        class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        min="1" required>
                </div>
                <div class="flex-1">
                    <label class="block mb-1 text-sm font-semibold">Discount Price (₹)</label>
                    <input type="number" name="discount_price"
                        class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        min="1">
                </div>
                <div class="flex-1">
                    <label class="block mb-1 text-sm font-semibold">Preparation Time (min)</label>
                    <input type="number" name="preparation_time"
                        class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        min="1">
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                <label class="flex items-center gap-2 text-gray-700">
                    <input type="checkbox" name="is_veg" id="isVeg"
                        class="h-4 w-4 rounded text-green-600 border-gray-300 focus:ring-green-500">
                    Veg
                </label>
                <label class="flex items-center gap-2 text-gray-700">
                    <input type="checkbox" name="is_available" id="isAvailable"
                        class="h-4 w-4 rounded text-green-600 border-gray-300 focus:ring-green-500" checked>
                    Available
                </label>
                <div>
                    <label class="sr-only">Status</label>
                    <select name="status"
                        class="border border-gray-300 rounded px-2 py-1 focus:ring-green-500 focus:border-green-500">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block mb-1 text-sm font-semibold">Currency</label>
                <input type="text" name="currency"
                    class="w-24 border border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-700" value="INR" readonly>
            </div>
            <div>
                <label class="block mb-1 text-sm font-semibold">Tags
                    <span class="text-xs text-gray-500">(comma separated)</span>
                </label>
                <input type="text" name="tags"
                    class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="e.g. paneer,chili">
            </div>
            <div>
                <label class="block mb-1 text-sm font-semibold">Menu
                    <span class="text-xs text-gray-500">(select one or more)</span>
                </label>
                <select name="menu" id="menuSelect" multiple autocomplete="off"
                    class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500 bg-white">
                    <!-- Example menu options -->
                    {{-- <option value="123">Lunch Specials</option>
                    <option value="321">Dinner Combos</option>
                    <option value="555">Weekend Treats</option>
                    <option value="888">Veg Delights</option>
                    <option value="132">Egg Lovers</option> --}}
                </select>
            </div>
            <div class="pt-2 flex justify-end">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold shadow">Add
                    Food</button>
            </div>
        </form>
    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        document.getElementById('addFoodForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Collect data and show as formatted JSON
            const data = Object.fromEntries(new FormData(this));
            data.is_veg = !!this.is_veg.checked;
            data.is_available = !!this.is_available.checked;
            data.tags = data.tags?.split(',').map(s => s.trim()).filter(Boolean) || [];

            const menuSelect = document.getElementById('menuSelect');
            data.menu = Array.from(menuSelect.selectedOptions).map(opt => opt.value);

            addFood(data)
        });

        async function fetchMenuItems() {
            const res = await httpRequest('/api/restaurants/{{ $restaurantId }}/menus');
            const menus = res?.data?.menus || [];

            if (menus.length == 0) {
                menuContainer.innerHTML = `<p class="mt-3 text-center">No menu available</p>`
                return;
            }

            let html = "";
            menus.forEach((item, idx) => {
                html += `<option value="${item.uid}">${item.name}</option>`
            });

            document.getElementById('menuSelect').innerHTML = html;
        }

        fetchMenuItems();

        async function addFood(data) {
            try {
                const res = await httpRequest('/api/restaurants/{{ $restaurantId }}/foods', {
                    method: "POST",
                    body: data
                });

                if (res.httpStatus >= 200 && res.httpStatus < 300) {
                    showToast('success', 'Food saved successfully!');
                    window.location.href = `foods/${res.data.food.uid}/images`
                }

            } catch (error) {
                console.log(error);
            }
        }
    </script>



@endsection
