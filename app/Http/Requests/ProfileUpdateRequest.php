<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Jogosultság ellenőrzése
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        // csak a saját profilját módosíthatja
        $targetId = (int) $this->route('id') ?: $user->id;

        if ($user->id !== $targetId) {
            Log::warning('Profile update denied: user tried to edit another profile', [
                'user_id'    => $user->id,
                'target_id'  => $targetId,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Validációs szabályok
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * Egyedi hibaüzenetek (opcionális, de szebb admin UI)
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'A név megadása kötelező.',
            'email.required' => 'Az email cím megadása kötelező.',
            'email.email'    => 'Érvényes email címet adj meg.',
            'email.unique'   => 'Ez az email cím már foglalt.',
        ];
    }
}

