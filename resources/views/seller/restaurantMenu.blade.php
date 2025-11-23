@extends('layouts.seller')

@section('title', 'Restaurant Menu')

@section('content')

    <div class="space-y-5 max-w-xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <div class="text-xl font-bold">Menus</div>
            <button id="btnAdd" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add Item</button>
        </div>
        <div id="menuContainer" class="space-y-2"></div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-5 max-w-md w-full">
            <h2 id="modalTitle" class="text-xl font-bold mb-4"></h2>
            <input id="menuNameInput" type="text" placeholder="Menu name"
                class="w-full border border-gray-300 rounded p-2 mb-4" />
            <div class="flex justify-end space-x-2">
                <button id="btnCancel" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Close</button>
                <button id="btnSave" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
            </div>
        </div>
    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        const menuContainer = document.getElementById('menuContainer');
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const menuNameInput = document.getElementById('menuNameInput');
        const btnAdd = document.getElementById('btnAdd');
        const btnCancel = document.getElementById('btnCancel');
        const btnSave = document.getElementById('btnSave');

        let menus = [];
        let editItemId = null;

        async function fetchMenuItems() {
            const res = await httpRequest('/api/restaurants/{{ $restaurantId }}/menus');
            menus = res?.data?.menus || [];
            renderMenuItems();
        }

        function renderMenuItems() {
            menuContainer.innerHTML = '';
            menus.forEach(item => {
                const card = document.createElement('div');
                card.className = 'bg-white shadow-md rounded-lg p-5 flex justify-between items-center';

                card.innerHTML = `
                    <div>
                        <h3 class="text-lg font-semibold">${item.name}</h3>
                    </div>
                    <div class="flex space-x-3">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 edit-btn" data-id="${item.uid}" data-name="${item.name}">Edit</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 delete-btn" data-id="${item.uid}">Delete</button>
                    </div>
                `;
                menuContainer.appendChild(card);
            });

            // Attach edit listeners
            document.querySelectorAll(".edit-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    editItemId = btn.getAttribute('data-id');
                    modalTitle.textContent = "Edit Menu Item";
                    menuNameInput.value = btn.getAttribute('data-name');
                    modal.classList.remove("hidden");
                });
            });

            // Attach delete listeners
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.getAttribute('data-id');
                    if (confirm('Delete this menu item?')) {
                        await deleteMenuItem(id);
                    }
                });
            });
        }

        btnAdd.addEventListener("click", () => {
            editItemId = null;
            modalTitle.textContent = "Add Menu Item";
            menuNameInput.value = "";
            modal.classList.remove("hidden");
        });

        btnCancel.addEventListener("click", () => {
            modal.classList.add("hidden");
        });

        btnSave.addEventListener("click", () => {
            if (editItemId) {
                updateMenuItem();
            } else {
                addMenuItem();
            }
        });

        async function addMenuItem() {
            try {
                const name = menuNameInput.value.trim();
                const res = await httpRequest('/api/restaurants/{{ $restaurantId }}/menus', {
                    method: "POST",
                    body: {
                        name
                    }
                });
                if (res.httpStatus >= 200 && res.httpStatus < 300) {
                    showToast('success', 'Menu created successfully!');
                    modal.classList.add("hidden");
                    fetchMenuItems();
                }
            } catch (error) {
                console.log(error);
            }
        }

        async function updateMenuItem() {
            try {
                const name = menuNameInput.value.trim();
                const res = await httpRequest(`/api/restaurants/{{ $restaurantId }}/menus/${editItemId}`, {
                    method: "PATCH",
                    body: {
                        name
                    }
                });
                if (res.httpStatus >= 200 && res.httpStatus < 300) {
                    showToast('success', 'Menu updated successfully!');
                    modal.classList.add("hidden");
                    fetchMenuItems();
                }
            } catch (error) {
                console.log(error);
            }
        }

        async function deleteMenuItem(id) {
            try {
                const res = await httpRequest(`/api/restaurants/{{ $restaurantId }}/menus/${id}`, {
                    method: "DELETE"
                });
                if (res.httpStatus >= 200 && res.httpStatus < 300) {
                    showToast('success', 'Menu deleted successfully!');
                    fetchMenuItems();
                }
            } catch (error) {
                console.log(error);
            }
        }

        fetchMenuItems();
    </script>

@endsection
