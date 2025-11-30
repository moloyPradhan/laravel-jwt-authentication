@extends('layouts.seller')

@section('title', 'Food Images')

@section('content')
    <div class="max-w-3xl mx-auto space-y-8 p-6 bg-white rounded-lg">

        {{-- Existing images list --}}
        <div>
            <h2 class="text-lg font-semibold mb-3">Existing Images</h2>

            <div id="images_list" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                {{-- Example server-side render; or you can fill this with JS --}}
                {{-- @foreach ($images as $image)
                    <div class="border rounded-lg overflow-hidden bg-gray-50">
                        <img src="{{ $image->url }}" alt="{{ $image->type }} image" class="w-full h-32 object-cover">
                        <div class="px-2 py-1 text-xs flex items-center justify-between">
                            <span class="font-medium capitalize">{{ $image->type }}</span>
                            <button type="button" class="text-red-500 hover:text-red-600 text-[11px] delete-image-btn"
                                data-image-id="{{ $image->id }}">
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach --}}
            </div>
        </div>

        {{-- Upload section --}}
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Upload Image</label>

            <div class="flex items-center gap-4 flex-wrap">

                {{-- Image type dropdown --}}
                <select id="image_type"
                    class="w-40 bg-white text-sm border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="main">Main</option>
                    <option value="thumbnail">Thumbnail</option>
                    <option value="gallery">Gallery</option>
                </select>

                <input id="food_image" name="food_image" type="file" accept="image/*" class="hidden image-input"
                    data-preview="image_preview">

                <button id="choose_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition">
                    Choose
                </button>

                <button id="upload_btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md shadow hover:bg-blue-600 transition">
                    Upload
                </button>
            </div>

            <div
                class="w-full h-60 mt-3 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
                <img id="image_preview" class="w-full h-full object-cover rounded" src="" alt="Preview"
                    style="display: none;">
                <span id="image_placeholder" class="text-gray-400 text-xs">No image</span>
            </div>
        </div>

    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        // preview
        document.querySelectorAll('.image-input').forEach((input) => {
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

        // choose & upload
        document.getElementById('choose_btn').addEventListener('click', () => {
            document.getElementById('food_image').click();
        });

        document.getElementById('upload_btn').addEventListener('click', () => {
            uploadImage();
        });

        async function uploadImage() {
            const input = document.getElementById('food_image');
            const typeSelect = document.getElementById('image_type');

            if (!input || input.files.length === 0) {
                showToast("warning", "No file selected for upload.");
                return;
            }

            const file = input.files[0];
            const selectedType = typeSelect.value;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('for', selectedType); // main | thumbnail | gallery

            try {
                const url = `/api/restaurants/{{ $restaurantId }}/foods/{{ $foodId }}/images`;
                const options = {
                    method: "POST",
                    body: formData,
                };

                const res = await httpRequest(url, options);

                if (res.httpStatus >= 200 && res.httpStatus < 300) {
                    showToast('success', 'Image uploaded successfully!');
                    // optional: append new card to #images_list using res.data
                } else {
                    showToast('error', res.message || 'Failed to upload image.');
                }
            } catch (err) {
                console.error("Upload error:", err);
                showToast('error', 'Something went wrong while uploading.');
            }
        }

        // delete existing image (optional wiring)
        document.querySelectorAll('.delete-image-btn').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.imageId;
                if (!id) return;

                try {
                    const url =
                        `/api/restaurants/{{ $restaurantId }}/foods/{{ $foodId }}/images/${id}`;
                    const options = {
                        method: "DELETE"
                    };
                    const res = await httpRequest(url, options);
                    if (res.httpStatus >= 200 && res.httpStatus < 300) {
                        showToast('success', 'Image deleted.');
                        btn.closest('div.border').remove();
                    } else {
                        showToast('error', res.message || 'Failed to delete.');
                    }
                } catch (err) {
                    console.error(err);
                    showToast('error', 'Delete failed.');
                }
            });
        });
    </script>
@endsection
