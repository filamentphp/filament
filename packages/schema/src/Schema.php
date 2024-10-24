<?php

namespace Filament\Schema;

use Filament\Schema\Contracts\HasSchemas;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\Drawer\Utils;

class Schema extends ViewComponent
{
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToModel;
    use Concerns\BelongsToParentComponent;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeValidated;
    use Concerns\Cloneable;
    use Concerns\HasColumns;
    use Concerns\HasComponents;
    use Concerns\HasEntryWrapper;
    use Concerns\HasFieldWrapper;
    use Concerns\HasInlineLabels;
    use Concerns\HasKey;
    use Concerns\HasOperation;
    use Concerns\HasState;
    use Concerns\HasStateBindingModifiers;
    use HasExtraAttributes;

    protected string $view = 'filament-schema::component-container';

    protected string $evaluationIdentifier = 'container';

    protected string $viewIdentifier = 'container';

    public static string $defaultCurrency = 'usd';

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultIsoDateDisplayFormat = 'L';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    public static string $defaultIsoDateTimeDisplayFormat = 'LLL';

    public static ?string $defaultNumberLocale = null;

    public static string $defaultTimeDisplayFormat = 'H:i:s';

    public static string $defaultIsoTimeDisplayFormat = 'LT';

    final public function __construct(Component & HasSchemas $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(Component & HasSchemas $livewire): static
    {
        $static = app(static::class, ['livewire' => $livewire]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! ($record instanceof Model)) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function toHtml(): string
    {
        if (! $this->shouldPartiallyRender()) {
            return parent::toHtml();
        }

        return Utils::insertAttributesIntoHtmlRoot(parent::toHtml(), [
            'wire:partial' => "schema.{$this->getKey()}",
        ]);
    }
}
