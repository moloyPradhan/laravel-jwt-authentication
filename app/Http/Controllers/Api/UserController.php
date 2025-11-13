<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use App\Models\User;


class UserController extends Controller
{
    use ApiResponse;

    public function listOtherUsers(Request $request)
    {
        $user = $request->user();

        $otherUsers = User::select('uid', 'name', 'status')
            ->where('id', '!=', $user->id)
            ->where('status', 'active')
            ->get();


        return $this->successResponse(
            200,
            'Other users',
            ['otherUsers' => $otherUsers]
        );
    }
}
