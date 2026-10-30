<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledContractMethods\CrossFile;

use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;

class ParentUser implements HasEmailAuthentication
{
    use EmailState;
}

class ChildUser extends ParentUser
{
    public function toggleEmailAuthentication(bool $condition): void
    {
        parent::toggleEmailAuthentication($condition);
    }
}

function unrelated(): object
{
    return new class
    {
        public function untouched(): bool
        {
            return true;
        }
    };
}
