<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Concerns\CanBeContained;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MorphToSelect extends Component
{
    use CanBeContained;
    use Concerns\CanAllowHtml;
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\CanBeNative;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\HasLoadingMessage;
    use HasLabel {
        getLabel as getBaseLabel;
    }
    use HasName;

    protected string $view = 'filament-schemas::components.fieldset';

    protected bool | Closure $isRequired = false;

    protected int | Closure $optionsLimit = 50;

    /**
     * @var array<Type> | Closure
     */
    protected array | Closure $types = [];

    protected ?Closure $modifyTypeSelectUsing = null;

    protected ?Closure $modifyKeySelectUsing = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $morphToSelectClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("MorphToSelect of class [$morphToSelectClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($morphToSelectClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(function (MorphToSelect $component): array {
            $relationship = $component->getRelationship();
            $typeColumn = $relationship->getMorphType();
            $keyColumn = $relationship->getForeignKeyName();

            $types = $component->getTypes();
            $isRequired = $component->isRequired();

            $selectedTypeKey = $component->getRawState()[$typeColumn] ?? null;
            $selectedType = $selectedTypeKey ? ($component->getTypes()[$selectedTypeKey] ?? null) : null;

            $typeSelect = Select::make($typeColumn)
                ->label($component->getLabel())
                ->hiddenLabel()
                ->options(array_map(
                    fn (Type $type): string => $type->getLabel(),
                    $types,
                ))
                ->native($component->isNative())
                ->required($isRequired)
                ->live()
                ->afterStateUpdated(function (Set $set) use ($component, $keyColumn): void {
                    $set($keyColumn, null);
                    $component->callAfterStateUpdated();
                });

            $keySelect = Select::make($keyColumn)
                ->label(fn (Get $get): ?string => ($types[$get($typeColumn)] ?? null)?->getLabel())
                ->hiddenLabel()
                ->options(fn (Select $component, Get $get): ?array => $component->evaluate(($types[$get($typeColumn)] ?? null)?->getOptionsUsing))
                ->getSearchResultsUsing(fn (Select $component, Get $get, $search): ?array => $component->evaluate(($types[$get($typeColumn)] ?? null)?->getSearchResultsUsing, ['search' => $search]))
                ->getOptionLabelUsing(fn (Select $component, Get $get, $value): ?string => $component->evaluate(($types[$get($typeColumn)] ?? null)?->getOptionLabelUsing, ['value' => $value]))
                ->native($component->isNative())
                ->required(fn (Get $get): bool => filled(($types[$get($typeColumn)] ?? null)))
                ->hidden(fn (Get $get): bool => blank(($types[$get($typeColumn)] ?? null)))
                ->dehydratedWhenHidden()
                ->searchable($component->isSearchable())
                ->searchDebounce($component->getSearchDebounce())
                ->searchPrompt($component->getSearchPrompt())
                ->searchingMessage($component->getSearchingMessage())
                ->noSearchResultsMessage($component->getNoSearchResultsMessage())
                ->loadingMessage($component->getLoadingMessage())
                ->allowHtml($component->isHtmlAllowed())
                ->optionsLimit($component->getOptionsLimit())
                ->preload($component->isPreloaded())
                ->when(
                    $component->isLive(),
                    fn (Select $component) => $component->live(onBlur: $this->isLiveOnBlur()),
                )
                ->afterStateUpdated(function () use ($component): void {
                    $component->callAfterStateUpdated();
                });

            if ($callback = $component->getModifyTypeSelectUsingCallback()) {
                $typeSelect = $component->evaluate($callback, [
                    'select' => $typeSelect,
                ]) ?? $typeSelect;
            }

            if ($callback = $component->getModifyKeySelectUsingCallback()) {
                $keySelect = $component->evaluate($callback, [
                    'select' => $keySelect,
                ]) ?? $keySelect;
            }

            if ($callback = $selectedType?->getModifyKeySelectUsingCallback()) {
                $keySelect = $component->evaluate($callback, [
                    'select' => $keySelect,
                ]) ?? $keySelect;
            }

            return [$typeSelect, $keySelect];
        });
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function modifyTypeSelectUsing(?Closure $callback): static
    {
        $this->modifyTypeSelectUsing = $callback;

        return $this;
    }

    public function modifyKeySelectUsing(?Closure $callback): static
    {
        $this->modifyKeySelectUsing = $callback;

        return $this;
    }

    public function getModifyTypeSelectUsingCallback(): ?Closure
    {
        return $this->modifyTypeSelectUsing;
    }

    public function getModifyKeySelectUsingCallback(): ?Closure
    {
        return $this->modifyKeySelectUsing;
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function required(bool | Closure $condition = true): static
    {
        $this->isRequired = $condition;

        return $this;
    }

    /**
     * @param  array<Type> | Closure  $types
     */
    public function types(array | Closure $types): static
    {
        $this->types = $types;

        return $this;
    }

    public function getRelationship(): MorphTo
    {
        $record = $this->getModelInstance();

        $relationshipName = $this->getName();

        if (! $record->isRelation($relationshipName)) {
            throw new Exception("The relationship [{$relationshipName}] does not exist on the model [{$this->getModel()}].");
        }

        return $record->{$relationshipName}();
    }

    /**
     * @return array<string, Type>
     */
    public function getTypes(): array
    {
        $types = [];

        foreach ($this->evaluate($this->types) as $type) {
            $types[$type->getAlias()] = $type;
        }

        return $types;
    }

    public function isRequired(): bool
    {
        return (bool) $this->evaluate($this->isRequired);
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function getLabel(): string | Htmlable | null
    {
        if (filled($label = $this->getBaseLabel())) {
            return $label;
        }

        $label = (string) str($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return $this->shouldTranslateLabel ? __($label) : $label;
    }
}
