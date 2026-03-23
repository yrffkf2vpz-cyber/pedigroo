<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController
{
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            Log::info('Login successful', [
                'user_id' => Auth::id(),
                'ip'      => $request->ip(),
            ]);

            return redirect()->intended('/admin');

        } catch (\Throwable $e) {

            Log::warning('Login failed', [
                'email' => $request->input('email'),
                'ip'    => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function destroy()
    {
        $userId = Auth::id();

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        Log::info('Logout successful', [
            'user_id' => $userId,
            'ip'      => request()->ip(),
        ]);

        return redirect('/');
    }
}

