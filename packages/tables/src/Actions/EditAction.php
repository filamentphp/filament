<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
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

        $this->icon(FilamentIcon::resolve('actions::edit-action') ?? 'heroicon-m-pencil-square');

        $this->fillForm(function (Model $record, Table $table): array {
            if ($translatableContentDriver = $table->makeTranslatableContentDriver()) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function (): void {
            $this->process(function (array $data, Model $record, Table $table) {
                $relationship = $table->getRelationship();

                $translatableContentDriver = $table->makeTranslatableContentDriver();

                if ($relationship instanceof BelongsToMany) {
                    $pivot = $record->{$relationship->getPivotAccessor()};

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
