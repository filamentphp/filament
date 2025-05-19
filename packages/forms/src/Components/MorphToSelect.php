<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MorphToSelect extends Component
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBeNative;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\HasLoadingMessage;
    use Concerns\HasName;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.fieldset';

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

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<Component>
     */
    public function getChildComponents(): array
    {
        $relationship = $this->getRelationship();
        $typeColumn = $relationship->getMorphType();
        $keyColumn = $relationship->getForeignKeyName();

        $types = $this->getTypes();
        $isRequired = $this->isRequired();

        /** @var ?Type $selectedType */
        $selectedType = $types[$this->evaluate(fn (Get $get): ?string => $get($typeColumn))] ?? null;

        $typeSelect = Select::make($typeColumn)
            ->label($this->getLabel())
            ->hiddenLabel()
            ->options(array_map(
                fn (Type $type): string => $type->getLabel(),
                $types,
            ))
            ->native($this->isNative())
            ->required($isRequired)
            ->live()
            ->afterStateUpdated(function (Set $set) use ($keyColumn) {
                $set($keyColumn, null);
                $this->callAfterStateUpdated();
            });

        $keySelect = Select::make($keyColumn)
            ->label($selectedType?->getLabel())
            ->hiddenLabel()
            ->options($selectedType?->getOptionsUsing)
            ->getSearchResultsUsing($selectedType?->getSearchResultsUsing)
            ->getOptionLabelUsing($selectedType?->getOptionLabelUsing)
            ->native($this->isNative())
            ->required(filled($selectedType))
            ->hidden(blank($selectedType))
            ->dehydratedWhenHidden()
            ->searchable($this->isSearchable())
            ->searchDebounce($this->getSearchDebounce())
            ->searchPrompt($this->getSearchPrompt())
            ->searchingMessage($this->getSearchingMessage())
            ->noSearchResultsMessage($this->getNoSearchResultsMessage())
            ->loadingMessage($this->getLoadingMessage())
            ->allowHtml($this->isHtmlAllowed())
            ->optionsLimit($this->getOptionsLimit())
            ->preload($this->isPreloaded())
            ->when(
                $this->isLive(),
                fn (Select $component) => $component->live(onBlur: $this->isLiveOnBlur()),
            )
            ->afterStateUpdated(function () {
                $this->callAfterStateUpdated();
            });

        if ($this->modifyTypeSelectUsing) {
            $typeSelect = $this->evaluate($this->modifyTypeSelectUsing, [
                'select' => $typeSelect,
            ]) ?? $typeSelect;
        }

        if ($this->modifyKeySelectUsing) {
            $keySelect = $this->evaluate($this->modifyKeySelectUsing, [
                'select' => $keySelect,
            ]) ?? $keySelect;
        }

        return [$typeSelect, $keySelect];
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
        return $this->getModelInstance()->{$this->getName()}();
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
}
