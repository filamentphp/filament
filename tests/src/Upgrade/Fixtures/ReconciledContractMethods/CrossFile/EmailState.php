<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledContractMethods\CrossFile;

trait EmailState
{
    public bool $enabled = false;

    public array $savedStates = [];

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->enabled = $condition;
        $this->savedStates[] = $condition;
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
