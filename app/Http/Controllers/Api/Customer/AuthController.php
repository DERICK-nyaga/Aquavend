<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //Registration

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : null,
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json(['customer' => $customer, 'token' => $token], 201);
    }

    //Password login

    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('phone', $validated['phone'])->first();

        if (! $customer || ! $customer->password || ! Hash::check($validated['password'], $customer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json(['customer' => $customer, 'token' => $token]);
    }

    //OTP: request

    public function requestOtp(Request $request, NotificationService $notifier)
    {
        $validated = $request->validate(['phone' => 'required|string']);

        $customer = Customer::where('phone', $validated['phone'])->first();

        if (! $customer) {
            return response()->json([
                'message' => 'No account found for this phone number. Please register first.',
            ], 404);
        }

        $otp = (string) random_int(100000, 999999);

        $customer->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $notifier->send($customer, 'otp_login', "Your Aquavend verification code is: {$otp}. Valid for 5 minutes.");

        return response()->json(['message' => 'OTP sent']);
    }

    //OTP: verify code, log in

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $customer = Customer::where('phone', $validated['phone'])->first();

        if (! $customer || $customer->otp_code !== $validated['otp'] || now()->greaterThan($customer->otp_expires_at)) {
            return response()->json(['message' => 'Invalid or expired code'], 401);
        }

        $customer->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'phone_verified_at' => $customer->phone_verified_at ?? now(),
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json(['customer' => $customer, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user('customer')->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user('customer'));
    }
}