<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantDocument;
use App\Models\RestaurantImages;

use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $data = Restaurant::with('user')->get();

        $filteredData = $data->map(function ($restaurant) {
            return [
                'uid'         => $restaurant->uid,
                'name'        => $restaurant->name,
                'phone'       => $restaurant->phone,
                'email'       => $restaurant->email,
                'description' => $restaurant->description,
                'status'      => $restaurant->status,
                'user_name'   => $restaurant->user ? $restaurant->user->name : null,
            ];
        });

        return $this->successResponse(
            200,
            'All Restaurants',
            [
                'restaurants' => $filteredData
            ]
        );
    }


    public function addRestaurant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'nullable|string|max:255',
            'phone'          => 'nullable|numeric',
            'email'          => 'nullable|string|max:255|email',
            'description'    => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                422,
                'Validation failed',
                [
                    'errors'  => $validator->errors()
                ]
            );
        }

        $user = $request->user();

        $hasPending = Restaurant::where('user_uid', $user->uid)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return $this->errorResponse(
                403,
                'You already have a restaurant with pending status. Please wait for approval before creating a new one.'
            );
        }

        $data = $validator->validated();
        $data['user_uid'] = $user->uid;

        $restaurants = Restaurant::create($data);

        return $this->successResponse(
            201,
            'Restaurant created successfully',
            [
                'addresses' => $restaurants
            ]
        );
    }

    public function getRestaurants(Request $request)
    {
        $user      = $request->user();
        $data      = $user->restaurants()->get();

        $filteredData = $data->map(function ($address) {
            return $address->only([
                'uid',
                'name',
                'phone',
                'email',
                'description',
                'status',
            ]);
        });

        return $this->successResponse(
            200,
            'User Restaurants',
            [
                'restaurants' => $filteredData
            ]
        );
    }

    public function addRestaurantDocuments(Request $request, string $uid)
    {
        // Validate files and types
        $validator = Validator::make($request->all(), [
            'fssai'        => 'required|file|mimes:pdf,jpg,jpeg,png',
            'pan'          => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {

            return $this->errorResponse(
                422,
                'Validation failed',
                $validator->errors()
            );
        }

        // Find target restaurant by UID
        $restaurant = Restaurant::where('uid', $uid)->first();
        if (!$restaurant) {

            return $this->errorResponse(
                404,
                'Restaurant not found',
                $validator->errors()
            );
        }

        // Types to process
        $types = ['fssai', 'pan'];
        $documents = [];

        foreach ($types as $type) {
            if ($request->hasFile($type)) {
                $file     = $request->file($type);
                $filePath = $file->store('restaurants/documents', 'public');

                $document = RestaurantDocument::create([
                    'restaurant_uid' => $restaurant->uid,
                    'type'           => $type,
                    'file_path'      => $filePath,
                ]);
                $documents[] = $document;
            }
        }


        return $this->successResponse(
            200,
            'Documents uploaded',
            [
                'documents' => $documents
            ]
        );
    }


    public function addRestaurantImages(Request $request, string $uid)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB limit
            'for'  => 'required|string|in:banner,front_image,logo,inside_image',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                422,
                'Validation failed',
                $validator->errors()
            );
        }

        // Find restaurant
        $restaurant = Restaurant::where('uid', $uid)->first();
        if (!$restaurant) {
            return $this->errorResponse(
                404,
                'Restaurant not found',
                []
            );
        }

        $file = $request->file('file');
        $for  = $request->input('for');

        $existingImage = RestaurantImages::where('restaurant_uid', $restaurant->uid)
            ->where('type', $for)
            ->first();

        // Store file
        $filePath = $file->store('restaurants/images', 'public');

        if ($existingImage) {
            // Delete old file from storage if it exists
            if (Storage::disk('public')->exists($existingImage->file_path)) {
                Storage::disk('public')->delete($existingImage->file_path);
            }

            // Update existing record
            $existingImage->update([
                'file_path' => $filePath,
            ]);

            $image = $existingImage;
            $message  = 'Image updated successfully';
        } else {
            // Create new record
            $image = RestaurantImages::create([
                'restaurant_uid' => $restaurant->uid,
                'type'           => $for,
                'file_path'      => $filePath,
            ]);
            $message = 'Image uploaded successfully';
        }

        return $this->successResponse(
            200,
            $message,
            ['image' => $image]
        );
    }
}
