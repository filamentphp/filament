<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RepeaterTest extends Page
{
    protected string $view = 'pages.repeater-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public string $reorderingView = '';

    public bool $hasRefreshed = false;

    public function mount(): void
    {
        $this->reorderingView = request()->query('reordering', '');
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        if ($this->reorderingView) {
            $hasVisibleMoveButtons = ! str_starts_with($this->reorderingView, 'screen-reader-');
            $reorderingView = str_replace('screen-reader-', '', $this->reorderingView);

            $repeater = Repeater::make('items')
                ->reorderableWithButtons($hasVisibleMoveButtons)
                ->reorderableWithDragAndDrop($reorderingView !== 'no-drag')
                ->reorderable($reorderingView !== 'disabled')
                ->schema([
                    TextInput::make('name')->required()->extraInputAttributes(['data-testid' => 'item-name']),
                    Repeater::make('children')
                        ->reorderableWithButtons($hasVisibleMoveButtons)
                        ->schema([TextInput::make('name')->extraInputAttributes(['data-testid' => 'child-name'])])
                        ->extraAttributes(['data-testid' => 'children']),
                ])
                ->itemLabel(static fn (array $state): ?string => $state['name'] ?? null)
                ->default([
                    ['name' => 'Alpha', 'children' => [['name' => 'Child A'], ['name' => 'Child B']]],
                    ['name' => 'Beta', 'children' => []],
                    ['name' => 'Gamma', 'children' => []],
                ])
                ->partiallyRenderAfterActionsCalled(! in_array($reorderingView, ['full', 'ancestor']))
                ->extraAttributes(['data-testid' => 'repeater']);

            if ($reorderingView === 'simple') {
                $repeater->simple(TextInput::make('name')->extraInputAttributes(['data-testid' => 'item-name']))
                    ->itemLabel(null)->default(['Alpha', 'Beta', 'Gamma']);
            } elseif ($reorderingView === 'table') {
                $repeater->schema([TextInput::make('name')->extraInputAttributes(['data-testid' => 'item-name', 'aria-label' => 'Name'])])
                    ->table([Repeater\TableColumn::make('Name')]);
            } elseif ($reorderingView === 'no-handle') {
                $repeater->reorderAction(static fn (Action $action): Action => $action->hidden());
            } elseif ($this->reorderingView === 'modal') {
                $repeater->moveDownAction(static fn (Action $action): Action => $action
                    ->requiresConfirmation()
                    ->modalSubmitAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'confirm-move']))
                    ->modalCancelAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'cancel-move'])));
            } elseif ($this->reorderingView === 'custom-mount') {
                $repeater->moveDownAction(static fn (Action $action): Action => $action->action("mountAction('chooseDestination')"));
            } elseif ($this->reorderingView === 'custom-refresh') {
                $repeater->moveDownAction(static fn (Action $action): Action => $action->action('refreshItems()'));
            } elseif (in_array($this->reorderingView, ['custom-child-cancel', 'custom-child-submit'])) {
                $repeater->moveDownAction(static fn (Action $action): Action => $action
                    ->registerModalActions([
                        Action::make('chooseChildDestination')
                            ->requiresConfirmation()
                            ->modalSubmitAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'submit-custom']))
                            ->modalCancelAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'cancel-custom']))
                            ->action(static fn (): null => null),
                    ])
                    ->action(static function (Action $action): void {
                        $action->getLivewire()->mountAction('chooseChildDestination');
                        $action->halt();
                    }));
            } elseif ($reorderingView === 'one-direction') {
                $repeater->moveUpAction(static fn (Action $action): Action => $action->hidden());
            }

            return $form->schema([$repeater])->partiallyRender($reorderingView === 'ancestor')->statePath('data');
        }

        return $form
            ->schema([
                Repeater::make('items')
                    ->label('Items')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                    ])
                    ->extraAttributes(['data-testid' => 'repeater']),
            ])
            ->statePath('data');
    }

    public function chooseDestinationAction(): Action
    {
        return Action::make('chooseDestination')
            ->requiresConfirmation()
            ->modalCancelAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'cancel-custom']))
            ->action(static fn (): null => null);
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function refreshItems(): void
    {
        $this->hasRefreshed = true;
        $this->forceRender();
    }
}
