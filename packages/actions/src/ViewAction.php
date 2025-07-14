<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ViewAction extends Action
{
    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'view';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::view.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::view.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitAction(false);
        $this->modalCancelAction(fn (Action $action) => $action->label(__('filament-actions::view.single.modal.actions.close.label')));

        $this->defaultColor('gray');

        $this->tableIcon(FilamentIcon::resolve(ActionsIconAlias::VIEW_ACTION) ?? Heroicon::Eye);
        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::VIEW_ACTION_GROUPED) ?? Heroicon::Eye);

        $this->disabledSchema();

        $this->fillForm(function (HasActions & HasSchemas $livewire, Model $record): array {
            if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
