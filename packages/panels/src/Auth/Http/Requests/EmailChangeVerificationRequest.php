<?php

namespace Filament\Auth\Http\Requests;

use Filament\Auth\Events\EmailChangeVerified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EmailChangeVerificationRequest extends FormRequest
{
    use Concerns\CanConsumeEmailChangeVerification;

    protected bool $wasEmailUpdateRejected = false;

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

        $wasConsumed = $this->consumeEmailChangeVerification($this->query('signature'));

        if ($isEmailTaken || (! $wasConsumed)) {
            return false;
        }

        $previousEmail = $user->getOriginal('email');

        $wasEmailUpdated = $user->update([
            'email' => $newEmail,
        ]);

        if (! $wasEmailUpdated) {
            $this->wasEmailUpdateRejected = true;

            return false;
        }

        $wasEmailChanged = $user->getOriginal('email') !== $previousEmail;

        if (method_exists($user, 'markEmailAsVerified')) {
            $user->markEmailAsVerified();
        }

        if (
            $wasEmailChanged &&
            ($user instanceof Authenticatable) &&
            is_string($previousEmail) &&
            ($previousEmail !== '')
        ) {
            event(new EmailChangeVerified($user, $previousEmail));
        }

        return true;
    }

    public function wasEmailUpdateRejected(): bool
    {
        return $this->wasEmailUpdateRejected;
    }

    public function withValidator(Validator $validator): Validator
    {
        return $validator;
    }
}
