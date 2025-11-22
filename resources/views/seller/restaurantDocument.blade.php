@extends('layouts.seller')

@section('title', 'Restaurant Images')

@section('content')
    <div class="max-w-2xl mx-auto space-y-8 p-6 bg-white rounded-lg">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Restaurant Images</h1>

        <!-- Front Image -->
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Front Image</label>
            <div class="flex items-center gap-4">
                <input id="front_image" name="front_image" type="file" accept="image/*" class="hidden image-input"
                    data-preview="front_preview">

                <button id="front_choose_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition">
                    Choose
                </button>

                <button id="front_upload_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md shadow hover:bg-blue-600 transition">
                    Upload
                </button>

                <div
                    class="w-full h-60 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
                    <img id="front_preview" class="w-full h-full object-cover rounded" src="" alt="Front preview"
                        style="display: none;">
                    <span id="front_placeholder" class="text-gray-400 text-xs">No image</span>
                </div>
            </div>
        </div>

        <!-- Inside Image -->
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Inside Image</label>
            <div class="flex items-center gap-4">
                <input id="inside_image" name="inside_image" type="file" accept="image/*" class="hidden image-input"
                    data-preview="inside_preview">

                <button id="inside_choose_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 transition">
                    Choose
                </button>

                <button id="inside_upload_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition">
                    Upload
                </button>

                <div
                    class="w-full h-60 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
                    <img id="inside_preview" class="w-full h-full object-cover rounded" src="" alt="Inside preview"
                        style="display: none;">
                    <span id="inside_placeholder" class="text-gray-400 text-xs">No image</span>
                </div>
            </div>
        </div>

        <!-- Banner Image -->
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Banner Image</label>
            <div class="flex items-center gap-4">
                <input id="banner" name="banner" type="file" accept="image/*" class="hidden image-input"
                    data-preview="banner_preview">

                <button id="banner_choose_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md shadow hover:bg-purple-700 transition">
                    Choose
                </button>

                <button id="banner_upload_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-purple-500 text-white rounded-md shadow hover:bg-purple-600 transition">
                    Upload
                </button>

                <div
                    class="w-full h-60 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
                    <img id="banner_preview" class="w-full h-full object-cover rounded" src="" alt="Banner preview"
                        style="display: none;">
                    <span id="banner_placeholder" class="text-gray-400 text-xs">No image</span>
                </div>
            </div>
        </div>

        <!-- Logo Image -->
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Logo</label>
            <div class="flex items-center gap-4">
                <input id="logo" name="logo_image" type="file" accept="image/*" class="hidden image-input"
                    data-preview="logo_preview">

                <button id="logo_choose_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition">
                    Choose
                </button>

                <button id="logo_upload_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md shadow hover:bg-red-600 transition">
                    Upload
                </button>

                <div
                    class="w-32 h-32 rounded-full overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
                    <img id="logo" class="w-full h-full object-cover rounded-full" src="" alt="Logo preview"
                        style="display: none;">
                    <span id="logo_placeholder" class="text-gray-400 text-xs">No image</span>
                </div>
            </div>
        </div>

    </div>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';


        async function getImages() {
            try {

                const url = `/api/restaurants/{{ $restaurantId }}/images`
                const res = await httpRequest(url);
                const images = res?.data?.images || [];

                // Find front_image and inside_image objects
                const frontImageObj = images.find(img => img.type === "front_image");
                const insideImageObj = images.find(img => img.type === "inside_image");
                const bannerImageObj = images.find(img => img.type === "banner");

                // Set image src if found
                if (frontImageObj) {
                    const preview = document.getElementById('front_preview');
                    const placeholder = document.getElementById('front_placeholder');
                    preview.src = frontImageObj.image;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }

                if (insideImageObj) {
                    const preview = document.getElementById('inside_preview');
                    const placeholder = document.getElementById('inside_placeholder');
                    preview.src = insideImageObj.image;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }

                if (bannerImageObj) {
                    const preview = document.getElementById('banner_preview');
                    const placeholder = document.getElementById('banner_placeholder');
                    preview.src = bannerImageObj.image;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }


            } catch (err) {
                console.log("Error :", err.message);
            }
        }

        getImages();

        const inputs = document.querySelectorAll('.image-input');

        inputs.forEach((input) => {
            input.addEventListener('change', (event) => {
                const file = event.target.files[0];
                const previewId = event.target.dataset.preview;
                const previewEl = document.getElementById(previewId);
                const placeholderId = previewId.replace('_preview', '_placeholder');
                const placeholderEl = document.getElementById(placeholderId);

                if (!previewEl || !placeholderEl) return;

                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewEl.src = e.target.result;
                        previewEl.style.display = 'block';
                        placeholderEl.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewEl.style.display = 'none';
                    placeholderEl.style.display = 'block';
                }
            });
        });

        // Attach event listeners for all choose and upload buttons
        document.getElementById('front_choose_btn').addEventListener('click', () => {
            document.getElementById('front_image').click();
        });
        document.getElementById('front_upload_btn').addEventListener('click', () => {
            uploadImage('front_image');
        });

        document.getElementById('inside_choose_btn').addEventListener('click', () => {
            document.getElementById('inside_image').click();
        });
        document.getElementById('inside_upload_btn').addEventListener('click', () => {
            uploadImage('inside_image');
        });

        document.getElementById('banner_choose_btn').addEventListener('click', () => {
            document.getElementById('banner').click();
        });
        document.getElementById('banner_upload_btn').addEventListener('click', () => {
            uploadImage('banner');
        });

        document.getElementById('logo_choose_btn').addEventListener('click', () => {
            document.getElementById('logo').click();
        });

        document.getElementById('logo_upload_btn').addEventListener('click', () => {
            uploadImage('logo');
        });

        async function uploadImage(fileInputId) {
            const input = document.getElementById(fileInputId);
            if (!input || input.files.length === 0) {
                alert("No file selected for upload.");
                return;
            }
            const file = input.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('for', fileInputId);

            try {
                const url = `/api/restaurants/{{ $restaurantId }}/images`;
                const options = {
                    method: "POST",
                    body: formData,
                };
                const response = await httpRequest(url, options);
                if (response && response.success) {
                    alert('Image uploaded successfully!');
                } else {
                    alert('Failed to upload image.');
                }
            } catch (err) {
                console.error("Upload error:", err);
                alert("Error uploading image.");
            }
        }
    </script>
@endsection
