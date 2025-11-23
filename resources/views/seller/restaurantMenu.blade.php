@extends('layouts.seller')

@section('title', 'Restaurant Menu')

@section('content')

    <div class="space-y-5 max-w-xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <div class="text-xl font-bold">Menus</div>
            <button id="btnAdd" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add Item</button>
        </div>
        <ul id="menuContainer" class="space-y-2"></ul>
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

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
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

            if(menus.length == 0){
                menuContainer.innerHTML=`<p class="mt-3 text-center">No menu available</p>`
                return;
            }

            renderMenuItems();
        }

        function renderMenuItems() {
            menuContainer.innerHTML = '';
            menus.forEach((item, idx) => {
                const li = document.createElement('li');
                li.setAttribute('data-id', item.uid);
                li.className = 'bg-white shadow-md rounded-lg p-5 flex justify-between items-center cursor-move';

                li.innerHTML = `
                    <div>
                        <h3 class="text-lg font-semibold">${item.name}</h3>
                    </div>
                    <div class="flex space-x-3">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 edit-btn" data-id="${item.uid}" data-name="${item.name}">Edit</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 delete-btn" data-id="${item.uid}">Delete</button>
                        <span class="text-gray-400 ml-2 select-none">☰</span>
                    </div>
                `;
                menuContainer.appendChild(li);
            });

            document.querySelectorAll(".edit-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    editItemId = btn.getAttribute('data-id');
                    modalTitle.textContent = "Edit Menu Item";
                    menuNameInput.value = btn.getAttribute('data-name');
                    modal.classList.remove("hidden");
                });
            });

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

        // Activate SortableJS after initial render
        let sortable = null;

        function activateSortable() {
            if (sortable) sortable.destroy(); // Prevent duplicate
            // Wait a tick for DOM rendering
            setTimeout(() => {
                sortable = Sortable.create(menuContainer, {
                    animation: 150,
                    handle: '.cursor-move, .text-gray-400',
                    onEnd: function(evt) {
                        // Get new order as array of UIDs
                        const newOrder = [...menuContainer.children].map(li => li.getAttribute(
                            'data-id'));


                        console.log('New order:', newOrder);
                    }
                });
            }, 10);
        }

        // Re-activate Sortable after each render
        const oldRenderMenuItems = renderMenuItems;
        renderMenuItems = function() {
            oldRenderMenuItems();
            activateSortable();
        };
    </script>

@endsection
