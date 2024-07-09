<?php

namespace Filament\Schema\Components;

use Filament\Schema\Components\Concerns\BelongsToContainer;
use Filament\Schema\Components\Concerns\BelongsToModel;
use Filament\Schema\Components\Concerns\CanBeConcealed;
use Filament\Schema\Components\Concerns\CanBeDisabled;
use Filament\Schema\Components\Concerns\CanBeHidden;
use Filament\Schema\Components\Concerns\CanBeRepeated;
use Filament\Schema\Components\Concerns\CanPartiallyRender;
use Filament\Schema\Components\Concerns\CanSpanColumns;
use Filament\Schema\Components\Concerns\Cloneable;
use Filament\Schema\Components\Concerns\HasActions;
use Filament\Schema\Components\Concerns\HasChildComponents;
use Filament\Schema\Components\Concerns\HasDecorations;
use Filament\Schema\Components\Concerns\HasEntryWrapper;
use Filament\Schema\Components\Concerns\HasFieldWrapper;
use Filament\Schema\Components\Concerns\HasId;
use Filament\Schema\Components\Concerns\HasInlineLabel;
use Filament\Schema\Components\Concerns\HasKey;
use Filament\Schema\Components\Concerns\HasLabel;
use Filament\Schema\Components\Concerns\HasMaxWidth;
use Filament\Schema\Components\Concerns\HasMeta;
use Filament\Schema\Components\Concerns\HasState;
use Filament\Schema\Concerns\HasColumns;
use Filament\Schema\Concerns\HasStateBindingModifiers;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;
use Livewire\Drawer\Utils;

class Component extends ViewComponent
{
    use BelongsToContainer;
    use BelongsToModel;
    use CanBeConcealed;
    use CanBeDisabled;
    use CanBeHidden;
    use CanBeRepeated;
    use CanGrow;
    use CanPartiallyRender;
    use CanSpanColumns;
    use Cloneable;
    use HasActions;
    use HasChildComponents;
    use HasColumns;
    use HasDecorations;
    use HasEntryWrapper;
    use HasExtraAttributes;
    use HasFieldWrapper;
    use HasId;
    use HasInlineLabel;
    use HasKey;
    use HasLabel;
    use HasMaxWidth;
    use HasMeta;
    use HasState;
    use HasStateBindingModifiers;

    protected string $evaluationIdentifier = 'component';

    protected string $viewIdentifier = 'schemaComponent';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'context', 'operation' => [$this->getContainer()->getOperation()],
            'get' => [$this->makeGetUtility()],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'rawState' => [$this->getRawState()],
            'record' => [$this->getRecord()],
            'set' => [$this->makeSetUtility()],
            'state' => [$this->getState()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! $record) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function toHtml(): string
    {
        $key = $this->getKey();

        if (blank($key)) {
            return parent::toHtml();
        }

        return Utils::insertAttributesIntoHtmlRoot(parent::toHtml(), [
            'wire:partial' => "schema-component::{$key}",
        ]);
    }
}
