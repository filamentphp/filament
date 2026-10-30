<?php

namespace Filament\Tests\Upgrade\Fixtures\InheritedContractCollision;

use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;

class ParentUser implements HasEmailAuthentication
{
    public bool $enabled = false;

    public function hasEmailAuthentication(): bool
    {
        return $this->enabled;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->enabled = $condition;
    }
}

class ChildUser extends ParentUser
{
    public function saveEmailAuthentication(bool $condition): void {}
}

class SiblingUser extends ParentUser
{
    public function toggleEmailAuthentication(bool $condition): void
    {
        parent::toggleEmailAuthentication($condition);
    }
}

class CleanUser implements HasEmailAuthentication
{
    public bool $enabled = false;

    public function hasEmailAuthentication(): bool
    {
        return $this->enabled;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->enabled = $condition;
    }
}

function enableClean(CleanUser $user): bool
{
    $user->toggleEmailAuthentication(true);

    return $user->hasEmailAuthentication();
}

function enableChild(ChildUser $user): bool
{
    $user->toggleEmailAuthentication(true);

    return $user->hasEmailAuthentication();
}

function enableParent(ParentUser $user): bool
{
    $user->toggleEmailAuthentication(true);

    return $user->hasEmailAuthentication();
}

function enableContract(HasEmailAuthentication $user): bool
{
    $user->toggleEmailAuthentication(true);

    return $user->hasEmailAuthentication();
}

function enableSibling(SiblingUser $user): bool
{
    $user->toggleEmailAuthentication(true);

    return $user->hasEmailAuthentication();
}
