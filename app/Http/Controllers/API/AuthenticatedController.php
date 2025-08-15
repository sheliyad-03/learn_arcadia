<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\MailSenderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\GlobalSetting\app\Models\MarketingSetting;

class AuthenticatedController extends Controller {
    public function register(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:4', 'max:100'],
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exist',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Confirm password does not match',
            'password.min' => 'You have to provide minimum 4 character password',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            // Create the user
            $user = User::create([
                'role'               => 'student',
                'name'               => $request->name,
                'email'              => $request->email,
                'status'             => 'active',
                'is_banned'          => 'no',
                'password'           => Hash::make($request->password),
                'verification_token' => Str::random(100),
            ]);

            if (!(new MailSenderService)->sendVerifyMailToUserFromTrait('single_user', $user)) {
                throw new \Exception('Failed to send email.');
            }
            DB::commit();

            $google_tagmanager_status = Setting::where('key', 'google_tagmanager_status')->value('value');
            $marketing_setting_register = MarketingSetting::where('key', 'register')->value('value');
            if ($user && $google_tagmanager_status == 'active' && $marketing_setting_register) {
                $register_user = [
                    'name'  => $user->name,
                    'email' => $user->email,
                ];
                session()->put('registerUser', $register_user);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'A varification link has been send to your mail, please verify and enjoy our service',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Registration failed due to an issue with sending the verification email. Please try again later.',
            ], 500);

        }
    }
    public function forgetPassword(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->forget_password_token = Str::random(100);
            $user->save();
            (new MailSenderService)->sendUserForgetPasswordFromTrait($user);

            return response()->json([
                'status'  => 'success',
                'message' => 'A password reset link has been send to your mail',
            ], 200);

        } else {
            return response()->json(['status' => 'error', 'message' => 'Email does not exist'], 404);
        }
    }
    public function resetPassword(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'forget_password_token' => ['required', 'string'],
            'email'                 => ['required', 'string', 'email'],
            'password'              => ['required', 'confirmed', 'min:4', 'max:100'],
        ], [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be 4 characters',
            'forget_password_token.required' => 'Forget password token is required',
            'password.confirmed'             => 'Confirm password does not match',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        // Find the user with the provided token and email
        $user = User::select('id', 'name', 'email', 'forget_password_token')->where('forget_password_token', $request->forget_password_token)->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid token, please try again',
            ], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->forget_password_token = null;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Password Reset successfully',
        ], 200);
    }
    public function login(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password match
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials please check your email and password'], 401);
        }
        // Check if user active
        if ($user->status != UserStatus::ACTIVE->value) {
            return response()->json(['status' => 'error', 'message' => 'Inactive account'], 403);
        }
        // Check if user is banned
        if ($user->is_banned == UserStatus::BANNED->value) {
            return response()->json(['status' => 'error', 'message' => 'Your account has been banned'], 403);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json(['status' => 'error', 'message' => 'Please verify your email'], 403);
        }
        //delete all extra token
        PersonalAccessToken::where('tokenable_id', $user->id)->where('tokenable_type', 'App\Models\User')->where('name', 'extra-token')->delete();

        $bearer_token = $user->createToken('student', ['*'])->plainTextToken;
        return response()->json(['status' => 'success', 'message' => 'Logged in successfully.', 'bearer_token' => $bearer_token, 'user_id'=>$user->id], 200);
    }
    public function logout(): JsonResponse {
        $user = auth()->user();
        //delete all extra token
        PersonalAccessToken::where('tokenable_id', $user->id)->where('tokenable_type', 'App\Models\User')->where('name', 'extra-token')->delete();
        $user->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully.'],200);

    }
    public function logoutAllApp(): JsonResponse {
        auth()->user()->tokens()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully.'],200);
    }
    public function checkAccessToken(): JsonResponse {
        return response()->json(['status' => 'success'],200);
    }
}
