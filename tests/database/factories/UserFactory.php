<?php

namespace Filament\Tests\Database\Factories;

use Filament\Auth\MultiFactor\EmailCode\EmailCodeAuthentication;
use Filament\Auth\MultiFactor\GoogleTwoFactor\GoogleTwoFactorAuthentication;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function hasEmailCodeAuthentication(): self
    {
        $emailCodeAuthentication = EmailCodeAuthentication::make();

        return $this->state(fn (): array => [
            'email_code_authentication_secret' => $emailCodeAuthentication->generateSecret(),
        ]);
    }

    /**
     * @param  ?array<string>  $recoveryCodes
     */
    public function hasGoogleTwoFactorAuthentication(?array $recoveryCodes = null): self
    {
        $googleTwoFactorAuthentication = GoogleTwoFactorAuthentication::make();

        return $this->state(fn (): array => [
            'google_two_factor_authentication_secret' => $googleTwoFactorAuthentication->generateSecret(),
            'google_two_factor_authentication_recovery_codes' => array_map(
                fn (string $code): string => Hash::make($code),
                $recoveryCodes ?? $googleTwoFactorAuthentication->generateRecoveryCodes(),
            ),
        ]);
    }
}
