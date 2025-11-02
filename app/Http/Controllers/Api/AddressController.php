<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;


class AddressController extends Controller
{
    use ApiResponse;

    /**
     * Get all addresses for authenticated user
     */

    public function index(Request $request)
    {
        $user      = $request->user();
        $addresses = $user->addresses()->active()->get();

        $filteredAddresses = $addresses->map(function ($address) {
            return $address->only([
                'uid',
                'label',
                'address_line_1',
                'city',
                'state',
                'country',
                'postal_code',
                'phone',
                'latitude',
                'longitude',
                'is_default'
            ]);
        });

        return $this->successResponse(
            200,
            'User addresses',
            [
                'addresses' => $filteredAddresses
            ]
        );
    }

    /**
     * Get addresses for a specific restaurant
     */
    // public function getRestaurantAddresses(string $restaurantUid)
    // {
    //     $restaurant = Restaurant::findByUidOrFail($restaurantUid);

    //     // Check if user owns the restaurant
    //     $user = jwt_user();
    //     if ($restaurant->user_id !== $user->id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized'
    //         ], 403);
    //     }

    //     $addresses = $restaurant->addresses()->active()->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $addresses
    //     ]);
    // }

    /**
     * Add address for authenticated user
     */

    public function addUserAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label'          => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'country'        => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',
            'phone'          => 'nullable|string|max:20',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'is_default'     => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        // $user = jwt_user();

        // If this is set as default, unset other default addresses
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($validator->validated());

        return $this->successResponse(
            201,
            'User address added successfully',
            [
                'data' => $address
            ],
        );
    }

    /**
     * Add address for restaurant
     */
    public function addRestaurantAddress(Request $request, string $restaurantUid)
    {
        $validator = Validator::make($request->all(), [
            'label'          => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'country'        => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',
            'phone'          => 'nullable|string|max:20',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'is_default'     => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $restaurant = Restaurant::findByUidOrFail($restaurantUid);

        // Check if user owns the restaurant
        // $user = jwt_user();

        $user = $request->user();

        if ($restaurant->user_uid !== $user->uid) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add address for this restaurant'
            ], 403);
        }

        // Convert all string fields to lowercase
        $validated = collect($validator->validated())
            ->map(function ($value) {
                return is_string($value) ? strtolower($value) : $value;
            })
            ->toArray();

        // If this is set as default, unset other default addresses
        if (!empty($validated['is_default']) && $validated['is_default']) {
            $restaurant->addresses()->update(['is_default' => false]);
        }

        // Create the address with lowercase data
        $address = $restaurant->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant address added successfully',
            'data'    => $address,
        ], 201);
    }

    /**
     * Display the specified address
     */
    public function show(string $uid)
    {
        // $address = Address::with('addressable')->findByUidOrFail($uid);

        $address = Address::with('addressable')
            ->where('uid', $uid)
            ->firstOrFail();

        return $this->successResponse(
            200,
            'Address',
            $address->only([
                'uid',
                'label',
                'address_line_1',
                'city',
                'state',
                'country',
                'postal_code',
                'phone',
                'latitude',
                'longitude',
                'is_default'
            ])
        );
    }

    /**
     * Update the specified address
     */
    // public function update(Request $request, string $uid)
    // {
    //     $address = Address::findByUidOrFail($uid);

    //     // Check authorization
    //     $user = jwt_user();
    //     $addressable = $address->addressable;

    //     // Check if user owns this address (either directly or through restaurant)
    //     if ($addressable instanceof \App\Models\User) {
    //         if ($addressable->id !== $user->id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized'
    //             ], 403);
    //         }
    //     } elseif ($addressable instanceof \App\Models\Restaurant) {
    //         if ($addressable->user_id !== $user->id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized'
    //             ], 403);
    //         }
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'label' => 'nullable|string|max:255',
    //         'address_line_1' => 'sometimes|string|max:255',
    //         'address_line_2' => 'nullable|string|max:255',
    //         'city' => 'sometimes|string|max:100',
    //         'state' => 'sometimes|string|max:100',
    //         'country' => 'sometimes|string|max:100',
    //         'postal_code' => 'sometimes|string|max:20',
    //         'phone' => 'nullable|string|max:20',
    //         'latitude' => 'nullable|numeric|between:-90,90',
    //         'longitude' => 'nullable|numeric|between:-180,180',
    //         'is_default' => 'boolean',
    //         'is_active' => 'boolean',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // If setting as default, unset other defaults
    //     if ($request->is_default) {
    //         Address::where('addressable_type', $address->addressable_type)
    //                ->where('addressable_id', $address->addressable_id)
    //                ->where('id', '!=', $address->id)
    //                ->update(['is_default' => false]);
    //     }

    //     $address->update($validator->validated());

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Address updated successfully',
    //         'data' => $address->fresh()
    //     ]);
    // }

    /**
     * Remove the specified address
     */
    // public function destroy(string $uid)
    // {
    //     $address = Address::findByUidOrFail($uid);

    //     // Check authorization
    //     $user = jwt_user();
    //     $addressable = $address->addressable;

    //     if ($addressable instanceof \App\Models\User) {
    //         if ($addressable->id !== $user->id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized'
    //             ], 403);
    //         }
    //     } elseif ($addressable instanceof \App\Models\Restaurant) {
    //         if ($addressable->user_id !== $user->id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized'
    //             ], 403);
    //         }
    //     }

    //     $address->delete();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Address deleted successfully'
    //     ]);
    // }

    /**
     * Set address as default
     */
    // public function setDefault(string $uid)
    // {
    //     $address = Address::findByUidOrFail($uid);

    //     // Check authorization
    //     $user = jwt_user();
    //     $addressable = $address->addressable;

    //     if ($addressable instanceof \App\Models\User) {
    //         if ($addressable->id !== $user->id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized'
    //             ], 403);
    //         }
    //     } elseif ($addressable instanceof \App\Models\Restaurant) {
    //         if ($addressable->user_id !== $user->id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized'
    //             ], 403);
    //         }
    //     }

    //     // Unset all defaults for this addressable
    //     Address::where('addressable_type', $address->addressable_type)
    //            ->where('addressable_id', $address->addressable_id)
    //            ->update(['is_default' => false]);

    //     // Set this as default
    //     $address->update(['is_default' => true]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Address set as default successfully',
    //         'data' => $address->fresh()
    //     ]);
    // }
}
