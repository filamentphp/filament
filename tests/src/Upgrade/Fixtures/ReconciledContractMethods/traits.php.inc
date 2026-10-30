<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledTraits;

use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;

trait SavesEmailAuthentication
{
    public bool $enabled = false;

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->enabled = $condition;
    }

    public function disable(): void
    {
        $this->toggleEmailAuthentication(false);
    }

    public function hasEmailAuthentication(): bool
    {
        return $this->enabled;
    }
}

trait NestedEmailAuthentication
{
    use SavesEmailAuthentication;
}

class ParentUser implements HasEmailAuthentication
{
    use NestedEmailAuthentication {
        toggleEmailAuthentication as remember;
    }
}

class ChildUser extends ParentUser
{
    public function enable(): void
    {
        parent::toggleEmailAuthentication(true);
    }
}

function configure(ChildUser $user): void
{
    $user->toggleEmailAuthentication(true);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledTraits;

use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;

trait SavesEmailAuthentication
{
    public bool $enabled = false;

    public function saveEmailAuthentication(bool $condition): void
    {
        $this->enabled = $condition;
    }

    public function disable(): void
    {
        $this->saveEmailAuthentication(false);
    }

    public function hasEmailAuthentication(): bool
    {
        return $this->enabled;
    }
}

trait NestedEmailAuthentication
{
    use SavesEmailAuthentication;
}

class ParentUser implements HasEmailAuthentication
{
    use NestedEmailAuthentication {
        saveEmailAuthentication as remember;
    }
}

class ChildUser extends ParentUser
{
    public function enable(): void
    {
        parent::saveEmailAuthentication(true);
    }
}

function configure(ChildUser $user): void
{
    $user->saveEmailAuthentication(true);
}
