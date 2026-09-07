<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class UnsavedChangesAlertBrowserTest extends Page
{
    protected string $view = 'pages.unsaved-changes-alert-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('readOnlyUnsavedChangesAlert')
                ->label('Read-only unsaved changes alert')
                ->schema([
                    TextInput::make('name'),
                ])
                ->disabledSchema()
                ->modalSubmitAction(false)
                ->extraAttributes(['data-testid' => 'read-only-unsaved-changes-alert-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'read-only-unsaved-changes-alert-modal']),
            Action::make('editableUnsavedChangesAlert')
                ->label('Editable unsaved changes alert')
                ->schema([
                    TextInput::make('name'),
                ])
                ->modalSubmitAction(false)
                ->extraAttributes(['data-testid' => 'editable-unsaved-changes-alert-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'editable-unsaved-changes-alert-modal']),
            Action::make('nestedUnsavedChangesAlert')
                ->label('Nested unsaved changes alert')
                ->modalHeading('Nested unsaved changes alert')
                ->modalSubmitAction(false)
                ->unsavedChangesAlert(false)
                ->extraAttributes(['data-testid' => 'nested-unsaved-changes-alert-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'nested-unsaved-changes-alert-modal'])
                ->extraModalFooterActions([
                    Action::make('editableNestedUnsavedChangesAlert')
                        ->label('Open editable nested action')
                        ->schema([
                            TextInput::make('name'),
                        ])
                        ->modalSubmitAction(false)
                        ->extraModalWindowAttributes(['data-testid' => 'editable-nested-unsaved-changes-alert-modal']),
                ]),
        ];
    }
}
