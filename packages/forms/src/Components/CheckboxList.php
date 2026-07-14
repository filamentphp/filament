<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\EnumArrayStateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Services\RelationshipJoiner;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use LogicException;

use function Filament\Support\generate_icon_html;

class CheckboxList extends Field implements Contracts\CanDisableOptions, Contracts\HasNestedRecursiveValidationRules, HasEmbeddedView
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBeSearchable;
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasDescriptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasOptions;
    use Concerns\HasPivotData;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.checkbox-list';

    protected string | Closure | null $relationshipTitleAttribute = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected ?Closure $getOptionDescriptionFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected bool | Closure $isBulkToggleable = false;

    protected ?Closure $modifySelectAllActionUsing = null;

    protected ?Closure $modifyDeselectAllActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchDebounce(0);

        $this->registerActions([
            fn (CheckboxList $component): Action => $component->getSelectAllAction(),
            fn (CheckboxList $component): Action => $component->getDeselectAllAction(),
        ]);
    }

    public function getSelectAllAction(): Action
    {
        $action = Action::make($this->getSelectAllActionName())
            ->label(__('filament-forms::components.checkbox_list.actions.select_all.label'))
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(Size::Small);

        if ($this->modifySelectAllActionUsing) {
            $action = $this->evaluate($this->modifySelectAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function selectAllAction(?Closure $callback): static
    {
        $this->modifySelectAllActionUsing = $callback;

        return $this;
    }

    public function getSelectAllActionName(): string
    {
        return 'selectAll';
    }

    public function getDeselectAllAction(): Action
    {
        $action = Action::make($this->getDeselectAllActionName())
            ->label(__('filament-forms::components.checkbox_list.actions.deselect_all.label'))
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(Size::Small);

        if ($this->modifyDeselectAllActionUsing) {
            $action = $this->evaluate($this->modifyDeselectAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function deselectAllAction(?Closure $callback): static
    {
        $this->modifyDeselectAllActionUsing = $callback;

        return $this;
    }

    public function getDeselectAllActionName(): string
    {
        return 'deselectAll';
    }

    public function relationship(string | Closure | null $name = null, string | Closure | null $titleAttribute = null, ?Closure $modifyQueryUsing = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->relationshipTitleAttribute = $titleAttribute;
        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;

        $cachedRecords = null;
        $cachedOptions = null;

        $this->options(static function (CheckboxList $component) use ($modifyQueryUsing, &$cachedRecords, &$cachedOptions): array {
            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            if ($component->hasOptionLabelFromRecordUsingCallback() || $component->hasOptionDescriptionFromRecordUsingCallback()) {
                if (
                    (! $modifyQueryUsing) &&
                    ($cachedRecords !== null)
                ) {
                    $records = $cachedRecords;
                } else {
                    $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                    if ($modifyQueryUsing) {
                        $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                            'query' => $relationshipQuery,
                        ]) ?? $relationshipQuery;
                    }

                    $records = $relationshipQuery->get();

                    if (! $modifyQueryUsing) {
                        $cachedRecords = $records;
                    }
                }

                if ($component->hasOptionDescriptionFromRecordUsingCallback()) {
                    $descriptions = $records
                        ->mapWithKeys(static fn (Model $record) => [
                            $record->{Str::afterLast($relationship->getQualifiedRelatedKeyName(), '.')} => $component->getOptionDescriptionFromRecord($record),
                        ])
                        ->toArray();

                    $component->descriptions($descriptions);
                }

                if ($component->hasOptionLabelFromRecordUsingCallback()) {
                    return $records
                        ->mapWithKeys(static fn (Model $record) => [
                            $record->{Str::afterLast($relationship->getQualifiedRelatedKeyName(), '.')} => $component->getOptionLabelFromRecord($record),
                        ])
                        ->toArray();
                }
            }

            if (
                (! $modifyQueryUsing) &&
                ($cachedOptions !== null)
            ) {
                return $cachedOptions;
            }

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipOrderByAttribute = $relationshipTitleAttribute;

                if (str_contains($relationshipOrderByAttribute, ' as ')) {
                    $relationshipOrderByAttribute = (string) str($relationshipOrderByAttribute)->before(' as ');
                }

                $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($relationshipOrderByAttribute));
            }

            if (str_contains($relationshipTitleAttribute, '->')) {
                if (! str_contains($relationshipTitleAttribute, ' as ')) {
                    $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                }
            } else {
                $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
            }

            $options = $relationshipQuery
                ->pluck($relationshipTitleAttribute, $relationship->getQualifiedRelatedKeyName())
                ->toArray();

            if (! $modifyQueryUsing) {
                $cachedOptions = $options;
            }

            return $options;
        });

        $this->loadStateFromRelationshipsUsing(static function (CheckboxList $component): void {
            $component->fillStateFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (CheckboxList $component): void {
            $component->saveStateToRelationship();
        });

        $this->dehydrated(false);

        return $this;
    }

    public function fillStateFromRelationship(): void
    {
        $relationship = $this->getRelationship();
        $relationshipName = $this->getRelationshipName();

        if (
            (! $this->modifyRelationshipQueryUsing) &&
            ($record = $this->getRecord()) instanceof Model &&
            $record->relationLoaded($relationshipName)
        ) {
            /** @var Collection $relatedRecords */
            $relatedRecords = $record->getRelationValue($relationshipName);

            $this->state(
                $relatedRecords
                    ->pluck($relationship->getRelatedKeyName())
                    ->map(static fn ($key): string => strval($key))
                    ->all(),
            );

            return;
        }

        if ($this->modifyRelationshipQueryUsing) {
            $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationship->getQuery(),
            ]);
        }

        /** @var Collection $relatedRecords */
        $relatedRecords = $relationship->getResults();

        $this->state(
            // Cast the related keys to a string, otherwise Livewire does not
            // know how to handle deselection.
            //
            // https://github.com/filamentphp/filament/issues/1111
            $relatedRecords
                ->pluck($relationship->getRelatedKeyName())
                ->map(static fn ($key): string => strval($key))
                ->all(),
        );
    }

    public function saveStateToRelationship(): void
    {
        $relationship = $this->getRelationship();
        $record = $this->getRecord();
        $relationshipName = $this->getRelationshipName();

        if ($this->modifyRelationshipQueryUsing) {
            $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationship->getQuery(),
            ]);
        }

        /** @var Collection $relatedRecords */
        $relatedRecords = $relationship->getResults();

        $state = $this->getState() ?? [];

        $recordsToDetach = array_diff(
            $relatedRecords
                ->pluck($relationship->getRelatedKeyName())
                ->map(static fn ($key): string => strval($key))
                ->all(),
            $state,
        );

        if (count($recordsToDetach) > 0) {
            $relationship->detach($recordsToDetach);
        }

        $pivotData = $this->getPivotData();

        if ($pivotData === []) {
            $relationship->sync($state, detaching: false);
            $record->unsetRelation($relationshipName);

            return;
        }

        $relationship->syncWithPivotValues($state, $pivotData, detaching: false);
        $record->unsetRelation($relationshipName);
    }

    public function bulkToggleable(bool | Closure $condition = true): static
    {
        $this->isBulkToggleable = $condition;

        return $this;
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getOptionLabelFromRecord(Model $record): string | Htmlable
    {
        return $this->evaluate(
            $this->getOptionLabelFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }

    public function getOptionDescriptionFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionDescriptionFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionDescriptionFromRecordUsingCallback(): bool
    {
        return $this->getOptionDescriptionFromRecordUsing !== null;
    }

    public function getOptionDescriptionFromRecord(Model $record): string | Htmlable | null
    {
        return $this->evaluate(
            $this->getOptionDescriptionFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }

    public function getRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->label === null && $this->getRelationship()) {
            $label = (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function getRelationship(): ?BelongsToMany
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        $record = $this->getModelInstance();

        if ($record->hasAttribute($name) || (! $record->isRelation($name))) {
            throw new LogicException("The relationship [{$name}] does not exist on the model [{$this->getModel()}].");
        }

        return $record->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function isBulkToggleable(): bool
    {
        return (bool) $this->evaluate($this->isBulkToggleable);
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            EnumArrayStateCast::class,
            ['enum' => $enum],
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        if ($this->hasCustomStateCasts() || filled($this->getEnum())) {
            return parent::getDefaultStateCasts();
        }

        return [app(OptionsArrayStateCast::class)];
    }

    /**
     * @return ?array<string>
     */
    public function getInValidationRuleValues(): ?array
    {
        $values = parent::getInValidationRuleValues();

        if ($values !== null) {
            return $values;
        }

        return array_keys($this->getEnabledOptions());
    }

    public function hasInValidationOnMultipleValues(): bool
    {
        return true;
    }

    public function toEmbeddedHtml(): string
    {
        $extraInputAttributeBag = $this->getExtraInputAttributeBag();
        $id = $this->getId();
        $isHtmlAllowed = $this->isHtmlAllowed();
        $gridDirection = $this->getGridDirection() ?? GridDirection::Column;
        $isBulkToggleable = $this->isBulkToggleable();
        $isDisabled = $this->isDisabled();
        $isSearchable = $this->isSearchable();
        $statePath = $this->getStatePath();
        $options = $this->getOptions();
        $livewireKey = $this->getLivewireKey();
        $wireModelAttribute = $this->applyStateBindingModifiers('wire:model');
        $hasError = $this->hasErrorForPath($statePath);

        $optionsAttributes = $this->getExtraAttributeBag()
            ->grid($this->getColumns(), $gridDirection)
            ->merge([
                'x-show' => $isSearchable ? 'visibleCheckboxListOptions.length' : null,
            ], escape: false)
            ->class(['fi-fo-checkbox-list-options']);

        ob_start(); ?>

        <div
            aria-labelledby="<?= e($id) ?>-label"
            role="group"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('checkbox-list', 'filament/forms')) ?>"
            x-data="checkboxListFormComponent({
                        livewireId: <?= Js::from($this->getLivewire()->getId()) ?>,
                    })"
            <?= $this->getExtraAlpineAttributeBag()->class(['fi-fo-checkbox-list'])->toHtml() ?>
        >
            <?php if (! $isDisabled) { ?>
                <?php if ($isSearchable) { ?>
                    <div <?= (new FilamentComponentAttributeBag)->class(['fi-input-wrp', 'fi-fo-checkbox-list-search-input-wrp'])->toHtml() ?>>
                        <div class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content fi-inline">
                            <?= generate_icon_html(
                                Heroicon::MagnifyingGlass,
                                FormsIconAlias::COMPONENTS_CHECKBOX_LIST_SEARCH_FIELD,
                                (new FilamentComponentAttributeBag)->color(IconComponent::class, 'gray'),
                            )?->toHtml() ?>
                        </div>

                        <div class="fi-input-wrp-content-ctn">
                            <input
                                aria-label="<?= e($this->getSearchPrompt()) ?>"
                                placeholder="<?= e($this->getSearchPrompt()) ?>"
                                type="search"
                                x-model.debounce.<?= $this->getSearchDebounce() ?>="search"
                                class="fi-input fi-input-has-inline-prefix"
                            />
                        </div>
                    </div>
                <?php } ?>

                <?php if ($isBulkToggleable && count($options)) { ?>
                    <div
                        x-cloak
                        class="fi-fo-checkbox-list-actions"
                        wire:key="<?= e($livewireKey) ?>.actions"
                    >
                        <span
                            x-show="! areAllCheckboxesChecked"
                            x-on:click="toggleAllCheckboxes()"
                            wire:key="<?= e($livewireKey) ?>.actions.select-all"
                        >
                            <?= $this->getAction('selectAll')->toHtml() ?>
                        </span>

                        <span
                            x-show="areAllCheckboxesChecked"
                            x-on:click="toggleAllCheckboxes()"
                            wire:key="<?= e($livewireKey) ?>.actions.deselect-all"
                        >
                            <?= $this->getAction('deselectAll')->toHtml() ?>
                        </span>
                    </div>
                <?php } ?>
            <?php } ?>

            <div <?= $optionsAttributes->toHtml() ?>>
                <?php if (count($options)) { ?>
                    <?php foreach ($options as $value => $label) { ?>
                        <div
                            wire:key="<?= e($livewireKey) ?>.options.<?= e($value) ?>"
                            <?php if ($isSearchable) { ?>
                                x-show="
                                    $el
                                        .querySelector('.fi-fo-checkbox-list-option-label')
                                        ?.innerText.toLowerCase()
                                        .includes(search.toLowerCase()) ||
                                        $el
                                            .querySelector('.fi-fo-checkbox-list-option-description')
                                            ?.innerText.toLowerCase()
                                            .includes(search.toLowerCase())
                                "
                            <?php } ?>
                            class="fi-fo-checkbox-list-option-ctn"
                        >
                            <label class="fi-fo-checkbox-list-option">
                                <input
                                    type="checkbox"
                                    <?= $extraInputAttributeBag
                                        ->merge([
                                            'disabled' => $isDisabled || $this->isOptionDisabled($value, $label),
                                            'value' => e($value),
                                            'wire:loading.attr' => 'disabled',
                                            $wireModelAttribute => $statePath,
                                            'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                        ], escape: false)
                                        ->class([
                                            'fi-checkbox-input',
                                            'fi-valid' => ! $hasError,
                                            'fi-invalid' => $hasError,
                                        ])
                                        ->toHtml() ?>
                                />

                                <div class="fi-fo-checkbox-list-option-text">
                                    <span class="fi-fo-checkbox-list-option-label">
                                        <?php if ($isHtmlAllowed) { ?>
                                            <?= $label ?>
                                        <?php } else { ?>
                                            <?= e($label) ?>
                                        <?php } ?>
                                    </span>

                                    <?php if ($this->hasDescription($value)) { ?>
                                        <p class="fi-fo-checkbox-list-option-description">
                                            <?= e($this->getDescription($value)) ?>
                                        </p>
                                    <?php } ?>
                                </div>
                            </label>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div wire:key="<?= e($livewireKey) ?>.empty"></div>
                <?php } ?>
            </div>

            <?php if ($isSearchable) { ?>
                <div
                    x-cloak
                    x-show="search && ! visibleCheckboxListOptions.length"
                    role="status"
                    aria-live="polite"
                    class="fi-fo-checkbox-list-no-search-results-message"
                >
                    <?= e($this->getNoSearchResultsMessage()) ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), labelTag: 'div');
    }
}
