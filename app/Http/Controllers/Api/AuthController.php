<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RefreshToken;

use App\Services\AuthCookieService;
use App\Traits\ApiResponse;

use App\Models\Cart;

use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    protected AuthCookieService $cookieService;

    public function __construct(AuthCookieService $cookieService)
    {
        $this->cookieService = $cookieService;
    }

    /**
     * Send chat message.
     *
     * @group Chat
     * @bodyParam room_id string required The chat room ID.
     * @bodyParam from_user string required The sender’s user ID.
     * @bodyParam message string required The message text.
     * @response 200 {
     *   "status": "Message sent successfully"
     * }
     */


    public function register(Request $request)
    {
        try {
            $request->validate([
                'name'     => 'required',
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                422,
                'validation_failed',
                $e->errors(),
            );
        }

        $code = rand(100000, 999999);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'status'            => 'pending',
            'verification_code' => $code,
        ]);

        if (!$user) {
            return $this->errorResponse(
                500,
                'Internal Server Error',
                ['internal_server_error']
            );
        }

        // Send verification email
        Mail::to($user->email)->queue(new \App\Mail\VerifyEmailCode($user, $code));

        return $this->successResponse(
            201,
            'User created. Check email for verification code.',
            [
                'user' => $user->only(['uid', 'email', 'status'])
            ],
        );
    }

    public function verifyUser(Request $request)
    {
        $request->validate(['email' => 'required|email', 'code' => 'required']);
        $user = User::where('email', $request->email)->where('verification_code', $request->code)->first();

        if (!$user) {
            return $this->errorResponse(
                400,
                'Invalid code or email',
                ['invalid_input']
            );
        }

        $user->update([
            'status'            => 'active',
            'verification_code' => null,
            'email_verified_at' => now(),
        ]);

        // Send onboarding email
        Mail::to($user->email)->queue(new \App\Mail\WelcomeEmail($user));

        return $this->successResponse(
            200,
            'Account verified successfully. You can now login.',
            [
                'user' => $user->only(['uid', 'email', 'status'])
            ],
        );
    }

    public function login(Request $request, AuthCookieService $cookieService)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return $this->errorResponse(
                401,
                'Invalid credentials',
                ['unauthorized_access']
            );
        }

        $user = auth('api')->user();

        $accessTtl =  config('jwt.ttl');
        $refreshTtl = config('jwt.refresh_ttl');

        $refreshTokenValue = Str::random(64);

        RefreshToken::create([
            'user_uid'   => $user->uid,
            'token'      => hash('sha256', $refreshTokenValue),
            'expires_at' => now()->addMinutes($refreshTtl),
            'revoked'    => false,
        ]);

        $guestUid = $request->cookie('guest_uid');

        if ($guestUid) {
            Cart::where('guest_uid', $guestUid)
                ->update([
                    'user_uid'  => $user->uid,
                    'guest_uid' => null, // optional but recommended
                ]);
        }


        $accessCookie  = $cookieService->makeAccessToken($token, $accessTtl);
        $refreshCookie = $cookieService->makeRefreshToken($refreshTokenValue, $refreshTtl);

        return $this->successResponse(
            200,
            'Login successful',
            [
                'access_token'  => $token,
                'refresh_token' => $refreshTokenValue,
                'expires_in'    => $accessTtl,
                'user'          => $user
            ]
        )
            ->cookie($accessCookie)
            ->cookie($refreshCookie);
    }

    public function logout(Request $request, AuthCookieService $cookieService)
    {
        $refreshValue = $request->cookie('refresh_token') ?? $request->input('refresh_token');

        if ($refreshValue) {
            RefreshToken::where('token', hash('sha256', $refreshValue))
                ->update(['revoked' => true]);
        }

        try {
            auth('api')->logout();
        } catch (\Exception $e) {
            // Token already invalid or expired — safe to ignore
        }

        return $this->successResponse(
            200,
            'Successfully logged out'
        )
            ->cookie($cookieService->forget('access_token'))
            ->cookie($cookieService->forget('refresh_token'));
    }


    public function refresh(Request $request, AuthCookieService $cookieService)
    {
        $refreshTokenValue = $request->cookie('refresh_token') ?? $request->input('refresh_token');

        if (! $refreshTokenValue) {
            return $this->errorResponse(400, 'Refresh token required');
        }

        $hashed = hash('sha256', $refreshTokenValue);

        $refreshToken = RefreshToken::where('token', $hashed)
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $refreshToken) {
            return $this->errorResponse(401, 'Invalid or expired refresh token');
        }

        $user = $refreshToken->user;

        if (! $user) {
            return $this->errorResponse(404, 'User not found for this refresh token');
        }

        // Revoke old refresh token
        $refreshToken->update(['revoked' => true]);

        // Create new tokens
        $newAccessToken = JWTAuth::fromUser($user);
        $accessTtl  = config('jwt.ttl');
        $refreshTtl = config('jwt.refresh_ttl');

        $newRefreshValue = Str::random(64);

        RefreshToken::create([
            'user_uid'   => $user->uid,
            'token'      => hash('sha256', $newRefreshValue),
            'expires_at' => now()->addMinutes($refreshTtl),
            'revoked'    => false,
        ]);

        // Create cookies
        $accessCookie  = $cookieService->makeAccessToken($newAccessToken, $accessTtl);
        $refreshCookie = $cookieService->makeRefreshToken($newRefreshValue, $refreshTtl);

        return $this->successResponse(
            200,
            'Token refreshed',
            [
                'access_token'  => $newAccessToken,
                'refresh_token' => $newRefreshValue,
                'expires_in'    => $accessTtl * 60,
                'user'          => $user->only(['uid', 'email', 'name', 'status']),
            ]
        )
            ->cookie($accessCookie)
            ->cookie($refreshCookie);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return $this->successResponse(
            200,
            'User details',
            [
                'user' => $user
            ]
        );
    }
}
