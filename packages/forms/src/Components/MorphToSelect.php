<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MorphToSelect extends Component
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\HasLoadingMessage;
    use Concerns\HasName;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.fieldset';

    public bool | Closure $isRequired = false;

    protected int | Closure $optionsLimit = 50;

    /**
     * @var array<Type> | Closure
     */
    public array | Closure $types = [];

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

        return [
            Select::make($typeColumn)
                ->label($this->getLabel())
                ->hiddenLabel()
                ->options(array_map(
                    fn (Type $type): string => $type->getLabel(),
                    $types,
                ))
                ->required($isRequired)
                ->reactive()
                ->afterStateUpdated(fn (Set $set) => $set($keyColumn, null)),
            Select::make($keyColumn)
                ->label($selectedType?->getLabel())
                ->hiddenLabel()
                ->options($selectedType?->getOptionsUsing)
                ->getSearchResultsUsing($selectedType?->getSearchResultsUsing)
                ->getOptionLabelUsing($selectedType?->getOptionLabelUsing)
                ->required($isRequired)
                ->hidden(! $selectedType)
                ->searchable($this->isSearchable())
                ->searchDebounce($this->getSearchDebounce())
                ->searchPrompt($this->getSearchPrompt())
                ->searchingMessage($this->getSearchingMessage())
                ->noSearchResultsMessage($this->getNoSearchResultsMessage())
                ->loadingMessage($this->getLoadingMessage())
                ->allowHtml($this->isHtmlAllowed())
                ->optionsLimit($this->getOptionsLimit())
                ->preload($this->isPreloaded()),
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

    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) str($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
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
