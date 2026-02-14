<?php

namespace App\Repositories\Implementations;

use App\Helpers\General;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Repositories\Interfaces\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if (!General::isUserActiveByEmail($data['email'])) {
            return [
                'success' => false,
                'message' => 'User is not active'
            ];
        }

        if (Auth::attempt($credentials)) {
            return [
                'success' => true,
                'message' => 'Login successful'
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();

        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }

    public function forgot(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found'
            ];
        }

        $token = General::generateRandomString(64);

        $data = [
            'email' => $user->email,
            'token' => $token,
            "created_at" => now()
        ];

        try {
            DB::beginTransaction();

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $data['email']],
                [
                    'email' => $data['email'],
                    'token' => Hash::make($data['token']),
                    'created_at' => $data['created_at']
                ]
            );

            $data += [
                'route' => "reset_password"
            ];

            Mail::to($user->email)->send(new ResetPasswordMail($data));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to send password reset link: ' . $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'Password reset link has been sent to your email',
            // 'token' => $token // For development/testing, remove this for production
        ];
    }

    public function reset(array $data)
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->first();

        if (!$resetRecord) {
            return [
                'success' => false,
                'message' => 'Invalid reset token'
            ];
        }

        if (!Hash::check($data['token'], $resetRecord->token)) {
            return [
                'success' => false,
                'message' => 'Invalid reset token'
            ];
        }

        $user = User::where('email', $data['email'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return [
            'success' => true,
            'message' => 'Password has been reset successfully'
        ];
    }

    public function validateResetToken($token, $email)
    {
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return false;
        }

        return Hash::check($token, $record->token);
    }
}
