<?php

namespace Filament\Schemas\Components;

use Filament\Schemas\Components\Concerns\BelongsToContainer;
use Filament\Schemas\Components\Concerns\BelongsToModel;
use Filament\Schemas\Components\Concerns\CanBeConcealed;
use Filament\Schemas\Components\Concerns\CanBeDisabled;
use Filament\Schemas\Components\Concerns\CanBeGridContainer;
use Filament\Schemas\Components\Concerns\CanBeHidden;
use Filament\Schemas\Components\Concerns\CanBeLiberatedFromContainerGrid;
use Filament\Schemas\Components\Concerns\CanBeRepeated;
use Filament\Schemas\Components\Concerns\CanPartiallyRender;
use Filament\Schemas\Components\Concerns\CanPoll;
use Filament\Schemas\Components\Concerns\Cloneable;
use Filament\Schemas\Components\Concerns\HasActions;
use Filament\Schemas\Components\Concerns\HasChildComponents;
use Filament\Schemas\Components\Concerns\HasEntryWrapper;
use Filament\Schemas\Components\Concerns\HasFieldWrapper;
use Filament\Schemas\Components\Concerns\HasHeadings;
use Filament\Schemas\Components\Concerns\HasId;
use Filament\Schemas\Components\Concerns\HasInlineLabel;
use Filament\Schemas\Components\Concerns\HasKey;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasMaxWidth;
use Filament\Schemas\Components\Concerns\HasMeta;
use Filament\Schemas\Components\Concerns\HasState;
use Filament\Schemas\Concerns\HasColumns;
use Filament\Schemas\Concerns\HasGap;
use Filament\Schemas\Concerns\HasStateBindingModifiers;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\CanSpanColumns;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;
use Livewire\Drawer\Utils;
use Livewire\Exceptions\RootTagMissingFromViewException;

class Component extends ViewComponent
{
    use BelongsToContainer;
    use BelongsToModel;
    use CanBeConcealed;
    use CanBeDisabled;
    use CanBeGridContainer;
    use CanBeHidden;
    use CanBeLiberatedFromContainerGrid;
    use CanBeRepeated;
    use CanGrow;
    use CanPartiallyRender;
    use CanPoll;
    use CanSpanColumns;
    use Cloneable;
    use HasActions;
    use HasChildComponents;
    use HasColumns;
    use HasEntryWrapper;
    use HasExtraAttributes;
    use HasFieldWrapper;
    use HasGap;
    use HasHeadings;
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
        if ($this->isLiberatedFromContainerGrid()) {
            return parent::toHtml();
        }

        $key = $this->getKey();

        if (blank($key)) {
            return parent::toHtml();
        }

        $html = parent::toHtml();

        if (blank($html)) {
            return '';
        }

        try {
            return Utils::insertAttributesIntoHtmlRoot($html, [
                'wire:partial' => "schema-component::{$key}",
            ]);
        } catch (RootTagMissingFromViewException) {
            return $html;
        }
    }
}
