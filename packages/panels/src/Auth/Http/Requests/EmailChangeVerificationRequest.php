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

        if ((! cache()->has("filament:auth:email_change:verification:{$signature}")) && (! cache()->has($signature))) {
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
        $cache = cache();
        $wasPending = false;
        $wasRemoved = true;

        foreach (["filament:auth:email_change:verification:{$signature}", $signature] as $cacheKey) {
            if (! $cache->has($cacheKey)) {
                continue;
            }

            $wasPending = true;

            if ((! $cache->forget($cacheKey)) && ($cache->get($cacheKey) !== null)) {
                $wasRemoved = false;
            }
        }

        if ($isEmailTaken || (! $wasPending) || (! $wasRemoved)) {
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
