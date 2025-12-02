<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


use App\Models\Restaurant;
use App\Models\RestaurantFood;
use App\Models\RestaurantFoodImage;
use App\Models\MenuFood;

class FoodController extends Controller
{
    use ApiResponse;

    public function addFood(Request $request, $restaurantId)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0|lt:price',
            'currency'          => 'required|string|size:3',
            'is_veg'            => 'required|boolean',
            'is_available'      => 'required|boolean',
            'preparation_time'  => 'nullable|integer|min:1|max:600',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|max:50',
            'status'            => 'required|in:active,inactive,deleted',
            'menu'              => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                422,
                'Validation failed',
                $validator->errors()
            );
        }

        $restaurant = Restaurant::where('uid', $restaurantId)->first();
        if (!$restaurant) {
            return $this->errorResponse(404, 'Restaurant not found', []);
        }

        $name             = $request->input('name');
        $description      = $request->input('description');
        $price            = $request->input('price');
        $discount_price   = $request->input('discount_price');
        $currency         = $request->input('currency');
        $is_veg           = $request->input('is_veg');
        $is_available     = $request->input('is_available');
        $preparation_time = $request->input('preparation_time');
        $tags             = $request->input('tags');
        $status           = $request->input('status');

        $menuIds          = $request->input('menu');

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            RestaurantFood::where('restaurant_uid', $restaurant->uid)
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        do {
            $code = strtoupper(Str::random(10));
        } while (RestaurantFood::where('code', $code)->exists());

        $food = RestaurantFood::create([
            'restaurant_uid'   => $restaurant->uid,
            'name'             => $name,
            'slug'             => $slug,
            'code'             => $code,
            'description'      => $description,
            'price'            => $price,
            'discount_price'   => $discount_price,
            'currency'         => $currency,
            'is_veg'           => $is_veg,
            'is_available'     => $is_available,
            'preparation_time' => $preparation_time,
            'tags'             => !empty($tags) ? json_encode($tags) : null,
            'status'           => $status,
        ]);

        $food->menus()->sync($menuIds);

        return $this->successResponse(201, 'Food item created successfully', [
            'food' => $food
        ]);
    }


    public function listRestaurantFood(Request $request, $restaurantId)
    {
        $foods = RestaurantFood::where([
            'restaurant_uid' => $restaurantId,
            'status'         => 'active'
        ])
            ->with('images')
            ->with('menus')
            ->get();

        $foods = $foods->map(function ($food) {
            $food->name = ucwords($food->name);
            $food->tags = !empty($food->tags) ? json_decode($food->tags, true) : [];

            // Map images and append full URL
            if (!empty($food->images)) {
                $food->images = $food->images->map(function ($img) {
                    $img->image_url = asset('storage/' . $img->image_url);

                    return $img;
                });
            }

            return $food;
        });

        return $this->successResponse(
            200,
            "Food list",
            ['foods' => $foods]
        );
    }

    public function addFoodImage(Request $request, $restaurantId, $foodId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB limit
            'for'  => 'required|string|in:main,thumbnail,gallery',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                422,
                'Validation failed',
                $validator->errors()
            );
        }

        // Find restaurant
        $restaurant = Restaurant::where('uid', $restaurantId)->first();
        if (!$restaurant) {
            return $this->errorResponse(
                404,
                'Restaurant not found',
                []
            );
        }

        $food = RestaurantFood::where('uid', $foodId)->first();
        if (!$food) {
            return $this->errorResponse(
                404,
                'Food not found',
                []
            );
        }

        $file = $request->file('file');
        $for  = $request->input('for');

        $existingImage = RestaurantFoodImage::where('food_uid', $foodId)
            ->where('image_type', $for)
            ->first();

        // Store file
        $filePath = $file->store('restaurants/foods/images', 'public');

        if ($existingImage) {
            // Delete old file from storage if it exists
            if (Storage::disk('public')->exists($existingImage->file_path)) {
                Storage::disk('public')->delete($existingImage->file_path);
            }

            // Update existing record
            $existingImage->update([
                'image_type' => $filePath,
            ]);

            $image   = $existingImage;
            $message  = 'Image updated successfully';
        } else {
            // Create new record
            $image = RestaurantFoodImage::create([
                'food_uid'       => $food->uid,
                'image_url'      => $filePath,
                'image_type'     => $for,
                'is_primary'     => $for == 'main' ? true : false
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
