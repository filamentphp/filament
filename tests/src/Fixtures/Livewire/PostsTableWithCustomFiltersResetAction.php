<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Table;

class PostsTableWithCustomFiltersResetAction extends PostsTable
{
    public string $resetActionPosition = 'header';

    public string $resetActionState = 'enabled';

    public string $resetActionView = 'button';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersLayout(match ($this->resetActionPosition) {
                'modal' => FiltersLayout::Modal,
                default => FiltersLayout::Dropdown,
            })
            ->filtersResetAction(
                function (Action $action): Action {
                    $action
                        ->label('Custom reset filters')
                        ->icon(Heroicon::XMark)
                        ->extraAttributes(['data-testid' => 'filters-reset-action']);

                    $action = match ($this->resetActionView) {
                        'link' => $action->link(),
                        default => $action->button(),
                    };

                    return match ($this->resetActionState) {
                        'disabled' => $action->disabled(),
                        'hidden' => $action->hidden(),
                        'invisible' => $action->visible(false),
                        'unauthorized' => $action->authorize(false),
                        'unauthorizedWithNotification' => $action
                            ->authorize(false)
                            ->authorizationMessage('You cannot reset filters')
                            ->authorizationNotification(),
                        default => $action,
                    };
                },
            )
            ->filtersResetActionPosition(match ($this->resetActionPosition) {
                'footer' => FiltersResetActionPosition::Footer,
                default => FiltersResetActionPosition::Header,
            });
    }
}
