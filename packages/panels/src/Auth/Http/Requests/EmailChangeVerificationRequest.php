<?php

namespace Filament\Auth\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EmailChangeVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! cache()->has($this->query('signature'))) {
            return false;
        }

        if (! hash_equals((string) $this->user()->getKey(), (string) $this->route('id'))) {
            return false;
        }

        try {
            return filled(decrypt($this->route('email')));
        } catch (DecryptException) {
            return false;
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [];
    }

    public function fulfill(): void
    {
        /** @var Model $user */
        $user = $this->user();

        $user->update([
            'email' => decrypt($this->route('email')),
        ]);

        cache()->forget($this->query('signature'));

        if (method_exists($user, 'markEmailAsVerified')) {
            $user->markEmailAsVerified();
        }
    }

    public function withValidator(Validator $validator): Validator
    {
        return $validator;
    }
}
