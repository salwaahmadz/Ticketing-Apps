<?php

namespace App\Helpers;

use App\Models\User;

class General
{
    /**
     * Generate random string
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqerstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomInt = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$randomInt];
        }

        return $randomString;
    }

    /**
     * Check if user is active
     *
     * @param string $email
     * @return bool
     */
    public static function isUserActiveByEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        return $user->is_active == 1;
    }
}
