<?php

namespace Filament\Auth\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BlockEmailChangeVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
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

    public function fulfill(): bool
    {
        return (bool) cache()->pull($this->query('verificationSignature'));
    }

    public function withValidator(Validator $validator): Validator
    {
        return $validator;
    }
}
