<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantDocument;

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
}
