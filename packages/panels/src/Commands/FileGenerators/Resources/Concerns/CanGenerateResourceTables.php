<?php

namespace Filament\Commands\FileGenerators\Resources\Concerns;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Commands\FileGenerators\Concerns\CanGenerateModelTables;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Nette\PhpGenerator\Literal;

trait CanGenerateResourceTables
{
    use CanGenerateModelTables;

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     */
    public function generateTableMethodBody(?string $model = null, array $exceptColumns = []): string
    {
        $this->importUnlessPartial(BulkActionGroup::class);

        $recordTitleAttributeOutput = '';

        if (filled($recordTitleAttribute = $this->getRecordTitleAttribute())) {
            $recordTitleAttributeOutput = new Literal(<<<'PHP'

                ->recordTitleAttribute(?)
            PHP, [$recordTitleAttribute]);
        }

        if (filled($headerActionsOutput = $this->outputTableHeaderActions())) {
            $headerActionsOutput = <<<PHP

                ->headerActions([
                    {$headerActionsOutput}
                ])
            PHP;
        }

        $modifyQueryOutput = '';

        if ($this->isSoftDeletable() && $this->hasTableModifyQueryForSoftDeletes()) {
            $this->namespace->addUse(Builder::class);
            $this->namespace->addUse(SoftDeletingScope::class);

            $modifyQueryOutput = <<<PHP

                ->modifyQueryUsing(fn ({$this->simplifyFqn(Builder::class)} \$query) => \$query
                    ->withoutGlobalScopes([
                        {$this->simplifyFqn(SoftDeletingScope::class)}::class,
                    ]))
            PHP;
        }

        return <<<PHP
            return \$table{$recordTitleAttributeOutput}
                ->columns([
                    {$this->outputTableColumns($model, $exceptColumns)}
                ])
                ->filters([
                    {$this->outputTableFilters()}
                ]){$headerActionsOutput}
                ->recordActions([
                    {$this->outputTableActions()}
                ])
                ->toolbarActions([
                    {$this->simplifyFqn(BulkActionGroup::class)}::make([
                        {$this->outputTableMethodBulkActions()}
                    ]),
                ]){$modifyQueryOutput};
            PHP;
    }

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     */
    public function outputTableColumns(?string $model = null, array $exceptColumns = []): string
    {
        $columns = $this->getTableColumns($model, $exceptColumns);

        if (empty($columns)) {
            $recordTitleAttribute = $this->getRecordTitleAttribute();

            if (blank($recordTitleAttribute)) {
                return '//';
            }

            $this->importUnlessPartial(TextColumn::class);

            return new Literal(<<<PHP
                {$this->simplifyFqn(TextColumn::class)}::make(?)
                            ->searchable(),
                PHP, [$recordTitleAttribute]);
        }

        return implode(PHP_EOL . '        ', $columns);
    }

    /**
     * @return array<class-string<BaseFilter>>
     */
    public function getTableFilters(): array
    {
        $filters = [];

        if ($this->isSoftDeletable()) {
            $filters[] = TrashedFilter::class;
        }

        foreach ($filters as $filter) {
            $this->importUnlessPartial($filter);
        }

        return $filters;
    }

    public function outputTableFilters(): string
    {
        $filters = $this->getTableFilters();

        if (empty($filters)) {
            return '//';
        }

        return implode(PHP_EOL . '        ', array_map(
            fn (string $filter) => "{$this->simplifyFqn($filter)}::make(),",
            $filters,
        ));
    }

    /**
     * @return array<class-string<Action>>
     */
    public function getTableHeaderActions(): array
    {
        $actions = [];

        if ($this->hasCreateTableAction()) {
            $actions[] = CreateAction::class;
        }

        if ($this->hasAttachTableActions()) {
            $actions[] = AttachAction::class;
        }

        if ($this->hasAssociateTableActions()) {
            $actions[] = AssociateAction::class;
        }

        foreach ($actions as $action) {
            $this->importUnlessPartial($action);
        }

        if ($this->hasPartialImports()) {
            $this->namespace->addUse('Filament\Actions');
        }

        return $actions;
    }

    public function outputTableHeaderActions(): ?string
    {
        $actions = $this->getTableHeaderActions();

        if (empty($actions)) {
            return null;
        }

        return implode(PHP_EOL . '        ', array_map(
            fn (string $action) => "{$this->simplifyFqn($action)}::make(),",
            $actions,
        ));
    }

    /**
     * @return array<class-string<Action>>
     */
    public function getTableActions(): array
    {
        $actions = [];

        if ($this->hasViewOperation()) {
            $actions[] = ViewAction::class;
        }

        $actions[] = EditAction::class;

        if ($this->hasAssociateTableActions()) {
            $actions[] = DissociateAction::class;
        }

        if ($this->hasAttachTableActions()) {
            $actions[] = DetachAction::class;
        }

        if ($this->hasDeleteTableActions()) {
            $actions[] = DeleteAction::class;

            if ($this->isSoftDeletable()) {
                $actions[] = ForceDeleteAction::class;
                $actions[] = RestoreAction::class;
            }
        }

        foreach ($actions as $action) {
            $this->importUnlessPartial($action);
        }

        return $actions;
    }

    public function hasCreateTableAction(): bool
    {
        return false;
    }

    public function hasAssociateTableActions(): bool
    {
        return false;
    }

    public function hasAttachTableActions(): bool
    {
        return false;
    }

    public function hasDeleteTableActions(): bool
    {
        return $this->isSimple();
    }

    public function outputTableActions(): string
    {
        return implode(PHP_EOL . '        ', array_map(
            fn (string $action) => "{$this->simplifyFqn($action)}::make(),",
            $this->getTableActions(),
        ));
    }

    /**
     * @return array<class-string<Action>>
     */
    public function getTableBulkActions(): array
    {
        $actions = [];

        if ($this->hasAssociateTableActions()) {
            $actions[] = DissociateBulkAction::class;
        }

        if ($this->hasAttachTableActions()) {
            $actions[] = DetachBulkAction::class;
        }

        $actions[] = DeleteBulkAction::class;

        if ($this->isSoftDeletable()) {
            $actions[] = ForceDeleteBulkAction::class;
            $actions[] = RestoreBulkAction::class;
        }

        foreach ($actions as $action) {
            $this->importUnlessPartial($action);
        }

        return $actions;
    }

    public function outputTableMethodBulkActions(): string
    {
        return implode(PHP_EOL . '            ', array_map(
            fn (string $action) => "{$this->simplifyFqn($action)}::make(),",
            $this->getTableBulkActions(),
        ));
    }

    public function hasTableModifyQueryForSoftDeletes(): bool
    {
        return false;
    }
}
