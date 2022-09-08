<?php

namespace Filament\Tables\Actions;

use Closure;
use Exception;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class AttachAction extends Action
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    protected ?Closure $modifyRecordSelectUsing = null;

    protected ?Closure $modifyRecordSelectOptionsQueryUsing = null;

    protected bool | Closure $isAttachAnotherDisabled = false;

    protected bool | Closure $isRecordSelectPreloaded = false;

    protected string | Closure | null $recordTitleAttribute = null;

    public static function getDefaultName(): ?string
    {
        return 'attach';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/attach.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/attach.single.modal.heading', ['label' => $this->getModelLabel()]));

        $this->modalButton(__('filament-support::actions/attach.single.modal.actions.attach.label'));

        $this->modalWidth('lg');

        $this->extraModalActions(function (): array {
            return $this->isAttachAnotherDisabled() ? [] : [
                $this->makeExtraModalAction('attachAnother', ['another' => true])
                    ->label(__('filament-support::actions/attach.single.modal.actions.attach_another.label')),
            ];
        });

        $this->successNotificationMessage(__('filament-support::actions/attach.single.messages.attached'));

        $this->color('secondary');

        $this->button();

        $this->form(fn (): array => [$this->getRecordSelect()]);

        $this->action(function (array $arguments, ComponentContainer $form): void {
            $this->process(function (array $data) {
                /** @var BelongsToMany $relationship */
                $relationship = $this->getRelationship();

                $record = $relationship->getRelated()->query()->find($data['recordId']);

                $relationship->attach(
                    $record,
                    Arr::only($data, $relationship->getPivotColumns()),
                );
            });

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $form->fill();

                $this->hold();

                return;
            }

            $this->success();
        });
    }

    public function recordSelect(?Closure $callback): static
    {
        $this->modifyRecordSelectUsing = $callback;

        return $this;
    }

    public function recordSelectOptionsQuery(?Closure $callback): static
    {
        $this->modifyRecordSelectOptionsQueryUsing = $callback;

        return $this;
    }

    public function recordTitleAttribute(string | Closure | null $attribute): static
    {
        $this->recordTitleAttribute = $attribute;

        return $this;
    }

    public function disableAttachAnother(bool | Closure $condition = true): static
    {
        $this->isAttachAnotherDisabled = $condition;

        return $this;
    }

    public function preloadRecordSelect(bool | Closure $condition = true): static
    {
        $this->isRecordSelectPreloaded = $condition;

        return $this;
    }

    public function isAttachAnotherDisabled(): bool
    {
        return $this->evaluate($this->isAttachAnotherDisabled);
    }

    public function isRecordSelectPreloaded(): bool
    {
        return $this->evaluate($this->isRecordSelectPreloaded);
    }

    public function getRecordTitleAttribute(): string
    {
        $attribute = $this->evaluate($this->recordTitleAttribute);

        if (blank($attribute)) {
            throw new Exception('Attach table action must have a `recordTitleAttribute()` defined, which is used to identify records to attach.');
        }

        return $attribute;
    }

    public function getRecordSelect(): Select
    {
        $getOptions = function (?string $search = null, ?array $searchColumns = []): array {
            /** @var BelongsToMany $relationship */
            $relationship = $this->getRelationship();

            $titleColumnName = $this->getRecordTitleAttribute();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($titleColumnName);

            if ($this->modifyRecordSelectOptionsQueryUsing) {
                $relationshipQuery = $this->evaluate($this->modifyRecordSelectOptionsQueryUsing, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if (filled($search)) {
                $search = strtolower($search);

                /** @var Connection $databaseConnection */
                $databaseConnection = $relationshipQuery->getConnection();

                $searchOperator = match ($databaseConnection->getDriverName()) {
                    'pgsql' => 'ilike',
                    default => 'like',
                };

                $searchColumns ??= [$titleColumnName];
                $isFirst = true;

                $relationshipQuery->where(function (Builder $query) use ($isFirst, $searchColumns, $searchOperator, $search): Builder {
                    foreach ($searchColumns as $searchColumnName) {
                        $whereClause = $isFirst ? 'where' : 'orWhere';

                        $query->{$whereClause}(
                            $searchColumnName,
                            $searchOperator,
                            "%{$search}%",
                        );

                        $isFirst = false;
                    }

                    return $query;
                });
            }

            $relatedKeyName = $relationship->getRelatedKeyName();

            return $relationshipQuery
                ->when(
                    ! $this->getLivewire()->allowsDuplicates(),
                    fn (Builder $query): Builder => $query->whereDoesntHave(
                        $this->getInverseRelationshipName(),
                        function (Builder $query): Builder {
                            return $query->where($this->getRelationship()->getParent()->getQualifiedKeyName(), $this->getRelationship()->getParent()->getKey());
                        },
                    ),
                )
                ->get()
                ->mapWithKeys(fn (Model $record): array => [$record->{$relatedKeyName} => $this->getRecordTitle($record)])
                ->toArray();
        };

        $select = Select::make('recordId')
            ->label(__('filament-support::actions/attach.single.modal.fields.record_id.label'))
            ->required()
            ->searchable()
            ->getSearchResultsUsing(static fn (Select $component, string $search): array => $getOptions(search: $search, searchColumns: $component->getSearchColumns()))
            ->getOptionLabelUsing(fn ($value): string => $this->getRecordTitle($this->getRelationship()->getRelated()->query()->find($value)))
            ->options(fn (): array => $this->isRecordSelectPreloaded() ? $getOptions() : [])
            ->disableLabel();

        if ($this->modifyRecordSelectUsing) {
            $select = $this->evaluate($this->modifyRecordSelectUsing, [
                'select' => $select,
            ]);
        }

        return $select;
    }
}
