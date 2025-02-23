<?php

namespace Filament\Auth\MultiFactor\Pages;

use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class SetUpRequiredMultiFactorAuthentication extends SimplePage
{
    public function mount(): void
    {
        if ((! Filament::hasMultiFactorAuthentication()) || $this->isEnabled()) {
            redirect()->intended(Filament::getProfileUrl() ?? Filament::getUrl());
        }
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.heading');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.subheading');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getMultiFactorAuthenticationContentComponent(),
                $this->getFooterActionsContentComponent(),
            ]);
    }

    public function getMultiFactorAuthenticationContentComponent(): Component
    {
        $user = Filament::auth()->user();

        return Section::make()
            ->compact()
            ->divided()
            ->secondary()
            ->schema(collect(Filament::getMultiFactorAuthenticationProviders())
                ->sort(fn (MultiFactorAuthenticationProvider $multiFactorAuthenticationProvider): int => $multiFactorAuthenticationProvider->isEnabled($user) ? 0 : 1)
                ->map(fn (MultiFactorAuthenticationProvider $multiFactorAuthenticationProvider): Component => Group::make($multiFactorAuthenticationProvider->getManagementSchemaComponents())
                    ->statePath($multiFactorAuthenticationProvider->getId()))
                ->all());
    }

    public function getFooterActionsContentComponent(): Component
    {
        return Actions::make($this->getFooterActions())
            ->fullWidth();
    }

    /**
     * @return array<Action>
     */
    public function getFooterActions(): array
    {
        return [
            $this->getContinueAction(),
        ];
    }

    public function getContinueAction(): Action
    {
        return Action::make('continue')
            ->label(__('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.actions.continue.label'))
            ->action(fn () => redirect()->intended(Filament::getUrl()))
            ->visible($this->isEnabled(...));
    }

    public function isEnabled(): bool
    {
        $user = Filament::auth()->user();

        foreach (Filament::getMultiFactorAuthenticationProviders() as $provider) {
            if ($provider->isEnabled($user)) {
                return true;
            }
        }

        return false;
    }
}
