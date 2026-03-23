<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            Log::warning('Login failed', [
                'email' => $this->input('email'),
                'ip'    => $this->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'A megadott adatok nem megfelelőek.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $user = Auth::user();

        if (!$user || !$user->is_active) {
            Auth::logout();

            Log::warning('Login denied: inactive user', [
                'email' => $this->input('email'),
                'ip'    => $this->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'A fiók inaktív vagy le van tiltva.',
            ]);
        }

        Log::info('Login successful', [
            'user_id' => $user->id,
            'ip'      => $this->ip(),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        Log::warning('Login rate limited', [
            'email' => $this->input('email'),
            'ip'    => $this->ip(),
        ]);

        throw ValidationException::withMessages([
            'email' => "Túl sok próbálkozás. Próbáld újra {$seconds} másodperc múlva.",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->string('email')).'|'.$this->ip();
    }
}

