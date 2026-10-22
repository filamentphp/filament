<?php

namespace Filament\Auth\MultiFactor\Pages;

use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Schema;
use Filament\Support\Attributes\ServerOnly;
use Illuminate\Contracts\Support\Htmlable;

class SetUpRequiredMultiFactorAuthentication extends SimplePage
{
    use RestrictsFileUploadsToSchemaComponents;

    #[ServerOnly]
    public function mount(): void
    {
        if ((! Filament::hasMultiFactorAuthentication()) || $this->isEnabled()) {
            redirect()->intended(Filament::getProfileUrl() ?? Filament::getUrl());
        }
    }

    #[ServerOnly]
    public function getTitle(): string | Htmlable
    {
        return __('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.title');
    }

    #[ServerOnly]
    public function getHeading(): string | Htmlable | null
    {
        return __('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.heading');
    }

    #[ServerOnly]
    public function getSubheading(): string | Htmlable | null
    {
        return __('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.subheading');
    }

    #[ServerOnly]
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getMultiFactorAuthenticationContentComponent(),
                $this->getFooterActionsContentComponent(),
            ]);
    }

    #[ServerOnly]
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

    #[ServerOnly]
    public function getFooterActionsContentComponent(): Component
    {
        return Actions::make($this->getFooterActions())
            ->fullWidth();
    }

    /**
     * @return array<Action>
     */
    #[ServerOnly]
    public function getFooterActions(): array
    {
        return [
            $this->getContinueAction(),
        ];
    }

    #[ServerOnly]
    public function getContinueAction(): Action
    {
        return Action::make('continue')
            ->label(__('filament-panels::auth/multi-factor/pages/set-up-required-multi-factor-authentication.actions.continue.label'))
            ->action(fn () => redirect()->intended(Filament::getUrl()))
            ->visible($this->isEnabled(...));
    }

    #[ServerOnly]
    public function isEnabled(): bool
    {
        return MultiFactorChallenge::make()->hasEnabledProviders(Filament::auth()->user());
    }
}
