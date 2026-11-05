<?php

namespace Filament\Tests\Upgrade\Fixtures\ContextualTesting;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Actions;
use Filament\Tests\Fixtures\Livewire\PostsTable;

// Reuse the shared action catalogue, adding only contexts it does not exercise.
class ContextualForms extends Actions
{
    public array $otherData = [];

    public function form(Schema $form): Schema
    {
        $form = parent::form($form);

        return $form->components([
            ...$form->getComponents(),
            TextInput::make('parent')->registerActions([
                Action::make('outer')
                    ->schema([
                        TextInput::make('child')->registerActions([
                            Action::make('inner')->label('Nested action'),
                        ]),
                    ]),
                Action::make('conditional')
                    ->label(fn (array $arguments): string => $arguments['label'] ?? 'Missing argument'),
            ]),
        ]);
    }

    public function other(Schema $schema): Schema
    {
        return $schema->statePath('otherData')->components([TextInput::make('different')]);
    }

    public function getDefaultTestingSchemaName(): ?string
    {
        return 'other';
    }

    public function getSchema(string $name): ?Schema
    {
        // PHP method names cannot be numeric; expose a real schema under a numeric alias.
        return parent::getSchema($name === '123' ? 'other' : $name);
    }
}

class ContextualTable extends PostsTable
{
    public function table(Table $table): Table
    {
        $table = parent::table($table);

        return $table->toolbarActions([
            ...$table->getToolbarActions(),
            Action::make('ordinaryToolbarAction'),
            BulkAction::make('lastBulkAction'),
        ]);
    }
}
