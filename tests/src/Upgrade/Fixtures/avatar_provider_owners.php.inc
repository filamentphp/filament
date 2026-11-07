<?php

namespace Filament\Tests\Upgrade\Fixtures\AvatarProviderOwners;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

trait AvatarImplementation
{
    public function get(Model $record): string
    {
        return '/trait.svg';
    }
}

trait NestedImplementation
{
    use AvatarImplementation;
}

class TraitProvider implements AvatarProvider
{
    use NestedImplementation;
}

class SharedTraitProvider implements AvatarProvider
{
    use AvatarImplementation;
}

class SharedTraitChild extends SharedTraitProvider
{
    public function get(Model $record): string
    {
        return parent::get($record);
    }
}

class AvatarParent
{
    public function get(?Model $record): string
    {
        return '/parent.svg';
    }
}

class InheritedProvider extends AvatarParent implements AvatarProvider {}

$inheritedConsumer = new class extends AvatarParent {};

class SiblingProvider extends AvatarParent implements AvatarProvider
{
    public function get(?Model $record): string
    {
        return parent::get($record);
    }
}

trait GuardedImplementation
{
    public function get(Model $record): string
    {
        return '/guarded.svg';
    }
}

class GuardedProvider implements AvatarProvider
{
    use GuardedImplementation;
}

class GuardedConsumer
{
    use GuardedImplementation;
}

class RestrictedChild extends GuardedConsumer
{
    public function get(Model|string $record): string
    {
        return '/restricted.svg';
    }
}

trait AdaptedImplementation
{
    public function get(Model $record): string
    {
        return '/adapted.svg';
    }
}

class AdaptedProvider implements AvatarProvider
{
    use AdaptedImplementation { get as originalGet; }
}

trait AliasedImplementation
{
    public function avatar(Model $record): string
    {
        return '/aliased.svg';
    }
}

class AliasedParent
{
    public function get(Model $record): string
    {
        return '/aliased-parent.svg';
    }
}

class AliasedProvider extends AliasedParent implements AvatarProvider
{
    use AliasedImplementation { avatar as get; }
}

class AnonymousParent
{
    public function get(Model $record): string
    {
        return '/anonymous.svg';
    }
}

class AnonymousFamilyProvider extends AnonymousParent implements AvatarProvider {}

$consumer = new class extends AnonymousParent
{
    public function get(Model|string $record): string
    {
        return '/anonymous-child.svg';
    }
};

trait ShadowedImplementation
{
    public function label(): string
    {
        return 'avatar';
    }

    public function get(Model $record): string
    {
        return $record->getAttribute('avatar');
    }
}

class OwnImplementationProvider implements AvatarProvider
{
    use ShadowedImplementation { label as avatarLabel; }

    public function get(Model $record): string
    {
        return '/own.svg';
    }
}

class PrivateParent
{
    private function get(Model|string $record): string
    {
        return '/private.svg';
    }
}

class PrivateFamilyProvider extends PrivateParent implements AvatarProvider
{
    public function get(Model $record): string
    {
        return '/public.svg';
    }
}

class Unrelated
{
    public function get(Model $record): string
    {
        return '/unrelated.svg';
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\AvatarProviderOwners;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

trait AvatarImplementation
{
    public function get(Model|Authenticatable $record): string
    {
        return '/trait.svg';
    }
}

trait NestedImplementation
{
    use AvatarImplementation;
}

class TraitProvider implements AvatarProvider
{
    use NestedImplementation;
}

class SharedTraitProvider implements AvatarProvider
{
    use AvatarImplementation;
}

class SharedTraitChild extends SharedTraitProvider
{
    public function get(Model|Authenticatable $record): string
    {
        return parent::get($record);
    }
}

class AvatarParent
{
    public function get(Model|Authenticatable|null $record): string
    {
        return '/parent.svg';
    }
}

class InheritedProvider extends AvatarParent implements AvatarProvider {}

$inheritedConsumer = new class extends AvatarParent {};

class SiblingProvider extends AvatarParent implements AvatarProvider
{
    public function get(Model|Authenticatable|null $record): string
    {
        return parent::get($record);
    }
}

trait GuardedImplementation
{
    public function get(Model $record): string
    {
        return '/guarded.svg';
    }
}

class GuardedProvider implements AvatarProvider
{
    use GuardedImplementation;
}

class GuardedConsumer
{
    use GuardedImplementation;
}

class RestrictedChild extends GuardedConsumer
{
    public function get(Model|string $record): string
    {
        return '/restricted.svg';
    }
}

trait AdaptedImplementation
{
    public function get(Model $record): string
    {
        return '/adapted.svg';
    }
}

class AdaptedProvider implements AvatarProvider
{
    use AdaptedImplementation { get as originalGet; }
}

trait AliasedImplementation
{
    public function avatar(Model $record): string
    {
        return '/aliased.svg';
    }
}

class AliasedParent
{
    public function get(Model $record): string
    {
        return '/aliased-parent.svg';
    }
}

class AliasedProvider extends AliasedParent implements AvatarProvider
{
    use AliasedImplementation { avatar as get; }
}

class AnonymousParent
{
    public function get(Model $record): string
    {
        return '/anonymous.svg';
    }
}

class AnonymousFamilyProvider extends AnonymousParent implements AvatarProvider {}

$consumer = new class extends AnonymousParent
{
    public function get(Model|string $record): string
    {
        return '/anonymous-child.svg';
    }
};

trait ShadowedImplementation
{
    public function label(): string
    {
        return 'avatar';
    }

    public function get(Model $record): string
    {
        return $record->getAttribute('avatar');
    }
}

class OwnImplementationProvider implements AvatarProvider
{
    use ShadowedImplementation { label as avatarLabel; }

    public function get(Model|Authenticatable $record): string
    {
        return '/own.svg';
    }
}

class PrivateParent
{
    private function get(Model|string $record): string
    {
        return '/private.svg';
    }
}

class PrivateFamilyProvider extends PrivateParent implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        return '/public.svg';
    }
}

class Unrelated
{
    public function get(Model $record): string
    {
        return '/unrelated.svg';
    }
}
