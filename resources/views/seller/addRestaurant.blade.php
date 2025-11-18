<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Restaurant</title>

    @include('layouts.headerLink')
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-lg">
        <h1 class="text-2xl font-semibold mb-6 text-gray-700">Add Restaurant</h1>

        <!-- Form -->
        <form id="restaurantForm" class="space-y-5" onsubmit="event.preventDefault();">

            <!-- Name -->
            <div>
                <label class="block text-gray-600 mb-1">Restaurant Name</label>
                <input type="text" id="name" name="name"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none"
                    placeholder="Enter restaurant name" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-600 mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none" placeholder="Enter description"></textarea>
            </div>

            <!-- Mobile -->
            <div>
                <label class="block text-gray-600 mb-1">Mobile</label>
                <input type="text" id="mobile" name="mobile"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none"
                    placeholder="Enter mobile number">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-600 mb-1">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 outline-none" placeholder="Enter email">
            </div>

            <!-- Submit -->
            <button type="button" id="btnAdd"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Add Restaurant
            </button>

        </form>
    </div>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';

        const urlSellerDashboard = @json(route('sellerDashboardPage'));

        async function addRestaurant() {
            try {
                const bodyData = {
                    name: document.getElementById("name").value,
                    description: document.getElementById("description").value,
                    phone: document.getElementById("mobile").value,
                    email: document.getElementById("email").value,
                };

                const options = {
                    method: "POST",
                    body: bodyData
                };

                const res = await httpRequest("/api/restaurants", options);
                if (res.statusCode == 2001) {
                    location.href = urlSellerDashboard
                }

            } catch (err) {
                console.log("Error:", err.message);
                alert("Failed to add restaurant!");
            }
        }

        document.getElementById('btnAdd').onclick = function(e) {
            addRestaurant();
        }
    </script>

</body>

</html>
