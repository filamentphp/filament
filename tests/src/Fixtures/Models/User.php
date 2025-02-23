<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Auth\MultiFactor\EmailCode\Contracts\HasEmailCodeAuthentication;
use Filament\Auth\MultiFactor\GoogleTwoFactor\Contracts\HasGoogleTwoFactorAuthentication;
use Filament\Auth\MultiFactor\GoogleTwoFactor\Contracts\HasGoogleTwoFactorAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Filament\Tests\Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasEmailCodeAuthentication, HasGoogleTwoFactorAuthentication, HasGoogleTwoFactorAuthenticationRecovery, HasTenants, MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'google_two_factor_authentication_secret',
        'google_two_factor_authentication_recovery_codes',
        'email_code_authentication_secret',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'json' => 'array',
        'email_verified_at' => 'datetime',
        'google_two_factor_authentication_secret' => 'encrypted',
        'google_two_factor_authentication_recovery_codes' => 'encrypted:array',
        'email_code_authentication_secret' => 'encrypted',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($panel->getId(), ['admin', 'slugs', 'google-two-factor-authentication', 'email-code-authentication', 'required-multi-factor-authentication']);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    public function getTenants(Panel $panel): array | Collection
    {
        return Team::all();
    }

    public function hasGoogleTwoFactorAuthentication(): bool
    {
        return filled($this->google_two_factor_authentication_secret);
    }

    public function getGoogleTwoFactorAuthenticationSecret(): string
    {
        return $this->google_two_factor_authentication_secret ?? '';
    }

    public function saveGoogleTwoFactorAuthenticationSecret(?string $secret): void
    {
        $this->google_two_factor_authentication_secret = $secret;
        $this->save();
    }

    public function getGoogleTwoFactorAuthenticationRecoveryCodes(): array
    {
        return $this->google_two_factor_authentication_recovery_codes ?? [];
    }

    public function saveGoogleTwoFactorAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->google_two_factor_authentication_recovery_codes = $codes;
        $this->save();
    }

    public function getGoogleTwoFactorAuthenticationHolderName(): string
    {
        return $this->email;
    }

    public function hasEmailCodeAuthentication(): bool
    {
        return filled($this->email_code_authentication_secret);
    }

    public function getEmailCodeAuthenticationSecret(): string
    {
        return $this->email_code_authentication_secret ?? '';
    }

    public function saveEmailCodeAuthenticationSecret(?string $secret): void
    {
        $this->email_code_authentication_secret = $secret;
        $this->save();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }
}
