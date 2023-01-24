<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
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

        $this->modalButton(__('filament-actions::edit.single.modal.actions.save.label'));

        $this->successNotificationTitle(__('filament-actions::edit.single.messages.saved'));

        $this->icon('heroicon-m-pencil-square');

        $this->fillForm(function (Model $record): array {
            $data = $record->attributesToArray();

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function (): void {
            $this->process(function (array $data, Model $record, Table $table) {
                $relationship = $table->getRelationship();

                if ($relationship instanceof BelongsToMany) {
                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        $record->{$relationship->getPivotAccessor()}->update($pivotData);
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                $record->update($data);
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
