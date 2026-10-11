<?php

namespace Filament\Tests\Upgrade\Fixtures\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider as Provider;
use Filament\AvatarProviders\UiAvatarsProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model as Record;

class CustomAvatarProvider implements Provider
{
    public function get(Record $user): string
    {
        return 'https://example.com/avatar.svg';
    }
}

interface CustomAvatarProviderContract extends Provider
{
    public function get(Record $record): string;
}

class InheritedAvatarProvider extends UiAvatarsProvider
{
    public function get(?Record $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class UnionAvatarProvider implements Provider
{
    public function get(Record | Authenticatable $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class MixedAvatarProvider implements Provider
{
    public function get(mixed $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class ObjectAvatarProvider implements Provider
{
    public function get(object $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class UntypedAvatarProvider implements Provider
{
    public function get($record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class UnrelatedProvider
{
    public function get(Record $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider as Provider;
use Filament\AvatarProviders\UiAvatarsProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model as Record;

class CustomAvatarProvider implements Provider
{
    public function get(\Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Auth\Authenticatable $user): string
    {
        return 'https://example.com/avatar.svg';
    }
}

interface CustomAvatarProviderContract extends Provider
{
    public function get(\Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Auth\Authenticatable $record): string;
}

class InheritedAvatarProvider extends UiAvatarsProvider
{
    public function get(\Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Auth\Authenticatable|null $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class UnionAvatarProvider implements Provider
{
    public function get(Record | Authenticatable $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class MixedAvatarProvider implements Provider
{
    public function get(mixed $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class ObjectAvatarProvider implements Provider
{
    public function get(object $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class UntypedAvatarProvider implements Provider
{
    public function get($record): string
    {
        return 'https://example.com/avatar.svg';
    }
}

class UnrelatedProvider
{
    public function get(Record $record): string
    {
        return 'https://example.com/avatar.svg';
    }
}
