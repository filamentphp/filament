<?php

namespace Filament\Tests\Upgrade\Fixtures\FormNaming;

use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Illuminate\Contracts\Auth\Authenticatable;

class Provider extends EmailAuthentication
{
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return parent::getChallengeFormComponents($user);
    }
}

function challenge(MultiFactorChallenge $challenge, Authenticatable $user): array
{
    return [
        $challenge->getSchemaComponents(user: $user),
        $challenge->getProviderPickerSchemaComponent($user),
        $challenge->getChallengeSchemaComponents($user),
    ];
}

function providerComponents(MultiFactorAuthenticationProvider $provider, Authenticatable $user): array
{
    return $provider->getChallengeFormComponents($user);
}
