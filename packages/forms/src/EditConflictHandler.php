<?php

namespace Filament\Forms;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Schemas\StateComparison;
use Filament\Support\Components\Component as BaseComponent;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditConflictHandler extends BaseComponent
{
    /** @var array<string> */
    protected array $ignoredStatePaths = [];

    /** @var array<string, array<string>> | null */
    protected ?array $statePathGroups = null;

    protected Schema | Closure $form;

    /** @var array<string, mixed> | Closure | null */
    protected array | Closure | null $outcome = null;

    protected Closure $getPreviewSchemaUsing;

    protected string $resolveMethod;

    protected string $discardMethod;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    /** @param array<string> $statePaths */
    public function ignoredStatePaths(array $statePaths): static
    {
        $this->ignoredStatePaths = $statePaths;

        return $this;
    }

    /** @param array<string, array<string>> | null $groups */
    public function statePathGroups(?array $groups): static
    {
        $this->statePathGroups = $groups;

        return $this;
    }

    /**
     * @internal The host supplies authorized forms and server-controlled comparison state.
     *
     * @param  array<string, mixed> | Closure | null  $outcome
     */
    public function context(Schema | Closure $form, array | Closure | null $outcome, Closure $getPreviewSchemaUsing, string $resolveMethod = 'resolveFormConflict', string $discardMethod = 'discardFormChanges'): static
    {
        $context = clone $this;
        $context->form = $form;
        $context->outcome = $outcome;
        $context->getPreviewSchemaUsing = $getPreviewSchemaUsing;
        $context->resolveMethod = $resolveMethod;
        $context->discardMethod = $discardMethod;

        return $context;
    }

    /** @return array<string, mixed> | null */
    public function getOutcome(): ?array
    {
        return $this->evaluate($this->outcome);
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $draft
     * @param  array<string, mixed>  $current
     */
    public function compare(array $base, array $draft, array $current): StateComparison
    {
        return StateComparison::make()
            ->base($base)
            ->draft($draft['state'])
            ->current($current['state'])
            ->ignoredStatePaths($this->ignoredStatePaths)
            ->statePathGroups($this->statePathGroups ?? [...$current['statePathGroups'], ...$draft['statePathGroups']])
            ->orderedStatePaths(array_values(array_unique([...$current['orderedStatePaths'], ...$draft['orderedStatePaths']])));
    }

    /**
     * @param  array<string, mixed>  $current
     * @return array<string, mixed>
     */
    public function acceptIncomingState(Schema $form, StateComparison $comparison, array $current): array
    {
        $base = $comparison->getBase();

        foreach ($comparison->getIncomingPaths() as $group) {
            $paths = $comparison->getStatePathGroups()[$group];
            $this->acceptCurrentState($form, $current, $paths);
            $base = $this->acceptBaseline($base, $comparison->getCurrent(), $paths);
        }

        return $base;
    }

    /**
     * Accepts a still-current displayed choice and returns its baseline, or `null` to show it again.
     *
     * @param  array<string, mixed>  $displayed
     * @param  array<string, mixed>  $current
     * @return array<string, mixed> | null
     */
    public function acceptChoice(Schema $form, StateComparison $comparison, array $displayed, array $current, string $statePath, string $source): ?array
    {
        $paths = $this->getChoiceStatePaths($comparison, $displayed, $statePath, $source);

        if ($paths === null) {
            return null;
        }

        if ($source === 'current') {
            $this->acceptCurrentState($form, $current, $paths);
        }

        return $this->acceptBaseline($comparison->getBase(), $comparison->getCurrent(), $paths);
    }

    /**
     * Returns the displayed paths a choice may accept, or `null` if it must be shown again.
     *
     * @param  array<string, mixed>  $displayed
     * @return array<string> | null
     */
    public function getChoiceStatePaths(StateComparison $comparison, array $displayed, string $statePath, string $source): ?array
    {
        if ((! in_array($statePath, $displayed['conflictingPaths'], true)) || (! in_array($source, ['draft', 'current'], true))) {
            return null;
        }

        $paths = $comparison->getStatePathGroups()[$statePath] ?? [];

        if ($paths !== $displayed['statePathGroups'][$statePath]) {
            return null;
        }

        foreach ($paths as $path) {
            foreach (['draft' => $comparison->getDraft(), 'current' => $comparison->getCurrent()] as $snapshot => $state) {
                if ((Arr::has($state, $path) !== Arr::has($displayed[$snapshot], $path)) || (data_get($state, $path) !== data_get($displayed[$snapshot], $path))) {
                    return null;
                }
            }
        }

        return $paths;
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $current
     * @param  array<string>  $paths
     * @return array<string, mixed>
     */
    public function acceptBaseline(array $base, array $current, array $paths): array
    {
        foreach ($paths as $path) {
            if (Arr::has($current, $path)) {
                data_set($base, $path, data_get($current, $path));
            } else {
                Arr::forget($base, $path);
            }
        }

        return $base;
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string>  $paths
     */
    public function acceptCurrentState(Schema $form, array $current, array $paths): void
    {
        foreach ($paths as $path) {
            $field = $form->getComponentByStatePath($path, withHidden: true);

            if ($field instanceof Field) {
                $field->rawState(data_get($current, $path));
            } else {
                $state = $this->acceptBaseline($form->getRawState(), $current, [$path]);
                $livewire = $form->getLivewire();
                data_set($livewire, $form->getStatePath(), $state);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function preserveUneditedNestedData(array $data, Model $record, StateComparison $comparison): array
    {
        $current = $record->attributesToArray();
        $paths = array_merge([], ...array_values($comparison->getStatePathGroups()));

        foreach ($data as $attribute => $value) {
            if ((! is_array($value)) || (! is_array($current[$attribute] ?? null)) || in_array($attribute, $paths, true)) {
                continue;
            }

            $merged = $current[$attribute];

            foreach ($paths as $path) {
                if (str_starts_with($path, "{$attribute}.") && Arr::has($data, $path)) {
                    data_set($merged, substr($path, strlen($attribute) + 1), data_get($data, $path));
                }
            }

            $data[$attribute] = $merged;
        }

        return $data;
    }

    /** @internal The caller must hold a transaction and reauthorize the scoped record. */
    public function lockRecord(Model $record): Model
    {
        $connection = $record->getConnection();

        if ($connection->getDriverName() === 'sqlite') {
            $keyName = $record->getKeyName();
            $connection->table($record->getTable())->whereRaw('0 = 1')
                ->update([$keyName => new Expression($connection->getQueryGrammar()->wrap($keyName))]);
        }

        return $record->newQueryWithoutScopes()->whereKey($record->getKey())->lockForUpdate()->firstOrFail();
    }

    /**
     * Reads an isolated, already-hydrated form without save-time callbacks.
     *
     * @return array{state: array<string, mixed>, rawState: array<string, mixed>, statePathGroups: array<string, array<string>>, orderedStatePaths: array<string>}
     */
    public function snapshot(Schema $form): array
    {
        $components = $form->getFlatComponents(withActions: false, withHidden: true);
        $prefix = $form->getStatePath() . '.';

        foreach ($components as $component) {
            $component->dehydrateStateUsing(null)->mutateDehydratedStateUsing(null);

            if ($component instanceof Repeater) {
                $component->mutateDehydratedStateUsing(static fn (Repeater $component, ?array $state): array => $component->dehydrateItems($state));
            } elseif ($component instanceof Builder) {
                $component->mutateDehydratedStateUsing(static fn (?array $state): array => array_values($state ?? []));
            }
        }

        $state = $form->getStateSnapshot();
        $formState = [];
        $rawFormState = [];
        $statePathGroups = [];
        $orderedStatePaths = [];

        foreach ($components as $component) {
            if (! ($component instanceof Field)) {
                continue;
            }

            foreach (array_keys($component->getStateToDehydrate($component->getRawState())) as $statePath) {
                $statePath = (string) str($statePath)->after($prefix);

                if (Arr::has($form->getRawState(), $statePath)) {
                    data_set($rawFormState, $statePath, data_get($form->getRawState(), $statePath));
                }
            }

            if (! $component->isDehydrated()) {
                continue;
            }

            $fieldStatePath = (string) str($component->getStatePath())->after($prefix);

            if (($component instanceof KeyValue) && $component->isReorderable()) {
                $orderedStatePath = $component->getStatePath();
                $parentComponent = $component->getContainer()->getParentComponent();

                while ($parentComponent !== null) {
                    if (($parentComponent instanceof Repeater) || ($parentComponent instanceof Builder)) {
                        $itemStatePathPattern = preg_quote($parentComponent->getStatePath(), '/') . '\.[^.]+';

                        if (($parentComponent instanceof Repeater) && ($simpleField = $parentComponent->getSimpleField())) {
                            $itemStatePathPattern .= '\.' . preg_quote($simpleField->getName(), '/');
                        }

                        $orderedStatePath = preg_replace('/^' . $itemStatePathPattern . '(?=\.|$)/', $parentComponent->getStatePath() . '.*', $orderedStatePath);
                    }

                    $parentComponent = $parentComponent->getContainer()->getParentComponent();
                }

                $orderedStatePaths[] = (string) str($orderedStatePath)->after($prefix);
            }

            foreach (array_keys($statePathGroups) as $parentStatePath) {
                if (str_starts_with($fieldStatePath, "{$parentStatePath}.")) {
                    continue 2;
                }
            }

            $statePathGroups[$fieldStatePath] = [];

            foreach (array_keys($component->getStateToDehydrate($component->getRawState())) as $statePath) {
                $statePath = (string) str($statePath)->after($prefix);
                $statePathGroups[$fieldStatePath][] = $statePath;

                if (Arr::has($state, $statePath)) {
                    data_set($formState, $statePath, data_get($state, $statePath));
                }
            }
        }

        array_walk_recursive($formState, static function (mixed &$value): void {
            if ($value instanceof TemporaryUploadedFile) {
                $value = [
                    'temporaryUpload' => hash('sha256', $value->getFilename()),
                    'name' => $value->getClientOriginalName(),
                ];
            }
        });

        return [
            'state' => json_decode(json_encode($formState, JSON_THROW_ON_ERROR), associative: true, flags: JSON_THROW_ON_ERROR),
            'rawState' => $rawFormState,
            'statePathGroups' => $statePathGroups,
            'orderedStatePaths' => $orderedStatePaths,
        ];
    }

    public function preparePreview(Schema $preview, string $source, string $statePath): Schema
    {
        $preview->key("{$source}FormConflictPreview")->statePath($statePath)->disabled();
        $preview->flushCachedAbsoluteStatePaths();

        foreach ($preview->getFlatComponents(withActions: true, withHidden: true) as $component) {
            if ($component instanceof ActionGroup) {
                $component->hidden();

                foreach ($component->getFlatActions() as $action) {
                    $action->hidden()->disabled();
                }

                continue;
            }

            if ($component instanceof Action) {
                $component->hidden()->disabled();

                continue;
            }

            $component
                ->id("{$source}FormConflictPreview.{$component->getId()}")
                ->disabled()->saved(false)->dehydrated(false)->poll(null)
                ->clearAfterStateUpdatedHooks()->afterStateUpdatedJs(null)->stateBindingModifiers([]);

            foreach ($component->getActions() as $action) {
                $action->hidden()->disabled();
            }

            if ($component instanceof Field) {
                $component->autofocus(false);
            }
        }

        return $preview;
    }

    public function getAction(): Action
    {
        return Action::make('resolveFormConflicts')
            ->disabled(fn (): bool => $this->getOutcome() === null)
            ->modalHeading(__('filament-forms::conflicts.heading'))
            ->modalDescription(__('filament-forms::conflicts.description'))
            ->modalWidth(Width::FourExtraLarge)
            ->schema(fn (): array => $this->getSchemaComponents())
            ->modalFooterActions(fn (Action $action): array => [
                $action->getModalCancelAction(),
                Action::make('discardFormChanges')
                    ->label(__('filament-forms::conflicts.actions.discard'))
                    ->color('danger')->link()->action($this->discardMethod)
                    ->extraAttributes(['data-testid' => 'stale-form-discard']),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('filament-forms::conflicts.actions.back'));
    }

    /** @return array<Component | Action | ActionGroup> */
    protected function getSchemaComponents(): array
    {
        return [
            Group::make(array_map(
                fn (string $statePath): Component => $this->getContentComponent($statePath),
                $this->getOutcome()['conflictingPaths'] ?? [],
            ))->extraAttributes(['data-testid' => 'stale-form-comparison']),
            Text::make(__('filament-forms::conflicts.unsaved')),
        ];
    }

    protected function getContentComponent(string $statePath): Component
    {
        return Section::make($this->getLabel($statePath))
            ->key("formConflict.{$statePath}")->contained(false)
            ->schema(array_map(
                fn (string $source): Component => Section::make(__("filament-forms::conflicts.{$source}"))
                    ->key($source)
                    ->icon($source === 'draft' ? Heroicon::OutlinedPencilSquare : Heroicon::OutlinedCloud)
                    ->compact()
                    ->schema([$this->getPreviewComponent($statePath, $source)])
                    ->footer(Actions::make([$this->getChoiceAction($statePath, $source)])->fullWidth()),
                ['draft', 'current'],
            ))->columns(['default' => 1, 'sm' => 2]);
    }

    protected function getLabel(string $statePath): string | Htmlable
    {
        $field = $this->evaluate($this->form)->getComponentByStatePath($statePath, withHidden: true);

        return ($field instanceof Field) ? ($field->getLabel() ?? $statePath) : $statePath;
    }

    protected function getPreviewComponent(string $statePath, string $source): Component
    {
        $previewForm = ($this->getPreviewSchemaUsing)($source);
        $fields = [];

        foreach ($this->getOutcome()['statePathGroups'][$statePath] as $path) {
            $field = $previewForm->getComponentByStatePath($path, withHidden: true);

            if ($field instanceof Field) {
                $fields[] = $field;
            }
        }

        return View::make('filament-forms::conflict-preview')->viewData(['fields' => $fields])
            ->extraAttributes(['data-testid' => "stale-form-preview-{$source}"]);
    }

    protected function getChoiceAction(string $statePath, string $source): Action
    {
        $label = $this->getLabel($statePath);

        return Action::make($source === 'draft' ? 'useDraft' : 'useCurrent')
            ->label(__("filament-forms::conflicts.actions.use_{$source}"))->color('gray')
            ->action($this->resolveMethod . '(' . Js::from($statePath) . ', ' . Js::from($source) . ')')
            ->extraAttributes([
                'aria-label' => e(__('filament-forms::conflicts.actions.use_' . $source . '_for_field', [
                    'field' => strip_tags($label instanceof Htmlable ? $label->toHtml() : $label),
                ])),
                'data-testid' => "stale-form-use-{$source}",
            ]);
    }
}
