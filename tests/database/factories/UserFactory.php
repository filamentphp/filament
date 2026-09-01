<?php

namespace Filament\Tests\Database\Factories;

use Filament\Auth\MultiFactor\App\AppAuthentication;
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

    public function hasEmailAuthentication(): self
    {
        return $this->state(fn (): array => [
            'has_email_authentication' => true,
        ]);
    }

    /**
     * @param  ?array<string>  $recoveryCodes
     */
    public function hasAppAuthentication(?array $recoveryCodes = null): self
    {
        $appAuthentication = AppAuthentication::make();

        return $this->state(fn (): array => [
            'app_authentication_secret' => $appAuthentication->generateSecret(),
            'app_authentication_recovery_codes' => array_map(
                fn (string $code): string => Hash::make($code),
                $recoveryCodes ?? $appAuthentication->generateRecoveryCodes(),
            ),
        ]);
    }
}
