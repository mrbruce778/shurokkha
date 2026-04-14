<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailOtpMail;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
   public function signIn(Request $request)
    {
        $request->validate([
            'phone_no' => 'required|string'
        ]);

        $user = User::where('phone_no', $request->phone_no)->first();

        // CASE 1: User exists and verified
        if ($user && $user->account_status === 'verified') {

            $user->api_token = bin2hex(random_bytes(30));
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $user->api_token
                ]
            ]);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        // CASE 2: Existing but not verified
        if ($user) {
            $user->update(['otp' => $otp]);
        } 
        // CASE 3: New user
        else {
            $user = User::create([
                'phone_no' => $request->phone_no,
                'otp' => $otp,
                'account_status' => 'pending',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent',
            'data' => [
                'phone_no' => $user->phone_no,
                'otp' => $otp // remove in production
            ]
        ]);
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_no' => 'required',
            'otp' => 'required'
        ]);

        $user = User::where('phone_no', $request->phone_no)->first();

        if (!$user || $user->otp != $request->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        // Mark verified
        $user->account_status = 'verified';
        $user->otp = null;
        $user->api_token = bin2hex(random_bytes(30));
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP verified',
            'data' => [
                'user' => $user,
                'token' => $user->api_token,
                'needs_account_type' => !$user->account_type
            ]
        ]);
    }
    public function updateprofile(Request $request)
    {
        // Step 1: Validate token
        $request->validate([
            'api_token' => 'required|string',
        ]);

        // Step 2: Find user
        $user = User::where('api_token', $request->api_token)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token'
            ], 401);
        }

        // Step 3: Validate other fields
        $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'username' => [
                'nullable',
                'string',
                Rule::unique('users')->ignore($user->id),
            ],
            'account_type' => 'nullable|in:men,women',
            'profile_image' => 'nullable|string', // base64 string
        ]);

        // Step 4: Handle base64 image (optional)
        if ($request->profile_image) {
            $image = $request->profile_image;

            // decode base64
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = time() . '.png';
            \File::put(public_path('uploads/' . $imageName), base64_decode($image));

            $user->profile_image = 'uploads/' . $imageName;
        }

        // Step 5: Update other fields
        $user->update($request->only([
            'full_name',
            'email',
            'username',
            'account_type'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
    public function logout(Request $request)
    {
        $request->validate([
            'api_token' => 'required|string',
        ]);
        $userId = $this->userIDFromToken($request->api_token);
        if(!$userId){
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
            
        }
        $user = User::find($userId);
        $user->api_token = null;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Logout Successful'
        ],200);
    }
}
