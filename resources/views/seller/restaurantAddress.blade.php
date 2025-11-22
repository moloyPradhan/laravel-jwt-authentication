@extends('layouts.seller')

@section('title', 'Seller Dashboard')

@section('content')

    <div class="space-y-5 max-w-xl">
        <div>
            <label for="address_line_1" class="block font-medium text-gray-700 mb-1">Address Line 1</label>
            <textarea name="address_line_1" class="w-full border border-gray-300 rounded px-3 py-2 outline-none" id="address_line_1"
                cols="30" rows="3" placeholder="Address Line 1"></textarea>
        </div>

        <div>
            <label for="address_line_2" class="block font-medium text-gray-700 mb-1">Address Line 2</label>
            <textarea name="address_line_2" class="w-full border border-gray-300 rounded px-3 py-2 outline-none" id="address_line_2"
                cols="30" rows="3" placeholder="Address Line 2"></textarea>
        </div>

        <div>
            <label for="city" class="block font-medium text-gray-700 mb-1">City</label>
            <input type="text" name="city" id="city" placeholder="Eg. Kolkata"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="state" class="block font-medium text-gray-700 mb-1">State</label>
            <input type="text" name="state" id="state" placeholder="Eg. West Bengal"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="country" class="block font-medium text-gray-700 mb-1">Country</label>
            <input type="text" name="country" id="country" value="India" placeholder="Eg. India"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="postal_code" class="block font-medium text-gray-700 mb-1">Postal Code</label>
            <input type="text" name="postal_code" id="postal_code" placeholder="Eg. 711114"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="latitude" class="block font-medium text-gray-700 mb-1">Latitude</label>
            <input type="text" name="latitude" id="latitude" placeholder="Eg. 88.211114"
                class="w-full border border-gray-300 rounded px-3 py-2 outline-none" />
        </div>

        <div>
            <label for="longitude" class="block font-medium text-gray-700 mb-1">Longitude</label>
            <input type="text" name="longitude" id="longitude" placeholder="Eg. 88.211114"
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


    </script>

@endsection
