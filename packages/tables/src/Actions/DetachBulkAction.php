<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetachBulkAction extends BulkAction
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    protected bool | Closure $allowsDuplicates = false;

    public static function make(string $name = 'detach'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/detach.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/detach.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalButton(__('filament-support::actions/detach.multiple.modal.actions.detach.label'));

        $this->successNotificationMessage(__('filament-support::actions/detach.multiple.messages.detached'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(function (Collection $records): void {
                /** @var BelongsToMany $relationship */
                $relationship = $this->getRelationship();

                if ($this->allowsDuplicates()) {
                    $records->each(
                        fn (Model $record) => $record->{$relationship->getPivotAccessor()}->delete(),
                    );
                } else {
                    $relationship->detach($records);
                }
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }

    public function allowDuplicates(bool | Closure $condition = true): static
    {
        $this->allowsDuplicates = $condition;

        return $this;
    }

    public function allowsDuplicates(): bool
    {
        return $this->evaluate($this->allowsDuplicates);
    }
}
