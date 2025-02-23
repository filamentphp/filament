<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MorphToSelect extends Component
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBeNative;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\HasLoadingMessage;
    use Concerns\HasName;

    protected string $view = 'filament-forms::components.fieldset';

    protected bool | Closure $isRequired = false;

    protected int | Closure $optionsLimit = 50;

    /**
     * @var array<Type> | Closure
     */
    protected array | Closure $types = [];

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

    public static function getDefaultName(): ?string
    {
        return null;
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getDefaultChildComponents(): array
    {
        $relationship = $this->getRelationship();
        $typeColumn = $relationship->getMorphType();
        $keyColumn = $relationship->getForeignKeyName();

        $types = $this->getTypes();
        $isRequired = $this->isRequired();

        $get = $this->makeGetUtility();

        /** @var ?Type $selectedType */
        $selectedType = $types[$get($typeColumn)] ?? null;

        return [
            Select::make($typeColumn)
                ->label($this->getLabel())
                ->hiddenLabel()
                ->options(array_map(
                    fn (Type $type): string => $type->getLabel(),
                    $types,
                ))
                ->native($this->isNative())
                ->required($isRequired)
                ->live()
                ->afterStateUpdated(function (Set $set) use ($keyColumn): void {
                    $set($keyColumn, null);
                    $this->callAfterStateUpdated();
                }),
            Select::make($keyColumn)
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
                ->afterStateUpdated(function (): void {
                    $this->callAfterStateUpdated();
                }),
        ];
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
}
