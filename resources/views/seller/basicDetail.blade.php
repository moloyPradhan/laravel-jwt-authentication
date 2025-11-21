@extends('layouts.seller')

@section('title', 'Seller Dashboard')

@section('content')

    <div class="space-y-5 max-w-xl">
        <div>
            <label for="name" class="block font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" id="name" placeholder="Eg. ABC Restaurant"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="description" class="block font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" class="w-full border border-gray-300 rounded px-3 py-2 outline-none" id="description"
                cols="30" rows="3" placeholder="Brief description about your restaurant"></textarea>
        </div>

        <div>
            <label for="phone" class="block font-medium text-gray-700 mb-1">Phone</label>
            <input type="tel" name="phone" id="phone"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" placeholder="Mobile or Telephone" />
        </div>

        <div>
            <label for="email" class="block font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" placeholder="Eg. abc@restaurant.com"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold transition">
                Save Changes
            </button>
        </div>
    </div>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';


        async function getBasicDetails() {
            try {

                const url = `/api/restaurants/{{ $restaurantId }}/basic-details`
                const res = await httpRequest(url);
                const basicDetails = res?.data?.basicDetails || [];

                document.getElementById('name').value = basicDetails?.name || "";
                document.getElementById('description').value = basicDetails?.description || "";
                document.getElementById('phone').value = basicDetails?.phone || "";
                document.getElementById('email').value = basicDetails?.email || "";

            } catch (err) {
                console.log("Error :", err.message);
            }
        }

        getBasicDetails();
    </script>

@endsection
