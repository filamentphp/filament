<?php

namespace Filament\Auth\MultiFactor\Contracts;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Auth\Authenticatable;

interface MultiFactorAuthenticationProvider
{
    public function isEnabled(Authenticatable $user): bool;

    public function getId(): string;

    public function getLoginFormLabel(): string;

    /**
     * @return array<Component| Action>
     */
    public function getManagementSchemaComponents(): array;

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getChallengeFormComponents(Authenticatable $user): array;
}
