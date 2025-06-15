<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Cek apakah user sudah terdaftar
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Buat user baru jika belum terdaftar
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(str_random(16)),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);

                // Berikan role default
                $user->assignRole('user');
            }

            // Login user
            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat autentikasi dengan ' . $provider);
        }
    }
} 