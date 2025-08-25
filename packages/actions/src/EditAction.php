<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class EditAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::edit.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::edit.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::edit.single.modal.actions.save.label'));

        $this->successNotificationTitle(__('filament-actions::edit.single.notifications.saved.title'));

        $this->defaultColor('primary');

        $this->tableIcon(FilamentIcon::resolve(ActionsIconAlias::EDIT_ACTION) ?? Heroicon::PencilSquare);
        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::EDIT_ACTION_GROUPED) ?? Heroicon::PencilSquare);

        $this->fillForm(function (HasActions & HasSchemas $livewire, Model $record, ?Table $table): array {
            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            if ($translatableContentDriver) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            $relationship = $table?->getRelationship();

            if ($relationship instanceof BelongsToMany) {
                $pivot = $record->getRelationValue($relationship->getPivotAccessor());

                $pivotColumns = $relationship->getPivotColumns();

                if ($translatableContentDriver) {
                    $data = [
                        ...$data,
                        ...Arr::only($translatableContentDriver->getRecordAttributesToArray($pivot), $pivotColumns),
                    ];
                } else {
                    $data = [
                        ...$data,
                        ...Arr::only($pivot->attributesToArray(), $pivotColumns),
                    ];
                }
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function (): void {
            $this->process(function (array $data, HasActions & HasSchemas $livewire, Model $record, ?Table $table): void {
                $relationship = $table?->getRelationship();

                $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

                if ($relationship instanceof BelongsToMany) {
                    $pivot = $record->getRelationValue($relationship->getPivotAccessor());

                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        if ($translatableContentDriver) {
                            $translatableContentDriver->updateRecord($pivot, $pivotData);
                        } else {
                            $pivot->update($pivotData);
                        }
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                if ($translatableContentDriver) {
                    $translatableContentDriver->updateRecord($record, $data);
                } else {
                    $record->update($data);
                }
            });

            $this->success();
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
