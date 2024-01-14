<?php

namespace Filament\Forms\Components;

class Hidden extends Field
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.hidden';

    protected string $evaluationIdentifier = 'hidden';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('hidden');
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'field', 'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
