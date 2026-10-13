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
        $signature = $this->query('signature');

        if (! cache()->has("filament:auth:email_change:verification:{$signature}")) {
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

    public function fulfill(): bool
    {
        /** @var Model $user */
        $user = $this->user();

        $newEmail = decrypt($this->route('email'));

        $isEmailTaken = $user::query()
            ->whereKeyNot($user->getKey())
            ->where('email', $newEmail)
            ->useWritePdo()
            ->exists();

        $signature = $this->query('signature');
        $cacheKey = "filament:auth:email_change:verification:{$signature}";
        $cache = cache();

        if ((! $cache->has($cacheKey)) || ((! $cache->forget($cacheKey)) && ($cache->get($cacheKey) !== null))) {
            return false;
        }

        if ($isEmailTaken) {
            return false;
        }

        $user->update([
            'email' => $newEmail,
        ]);

        if (method_exists($user, 'markEmailAsVerified')) {
            $user->markEmailAsVerified();
        }

        return true;
    }

    public function withValidator(Validator $validator): Validator
    {
        return $validator;
    }
}
