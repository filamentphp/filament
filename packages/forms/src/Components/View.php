<?php

namespace Filament\Forms\Components;

class View extends Component
{
    protected string $evaluationIdentifier = 'view';

    /**
     * @param  view-string  $view
     */
    final public function __construct(string $view)
    {
        $this->view($view);
    }

    /**
     * @param  view-string  $view
     */
    public static function make(string $view): static
    {
        $static = app(static::class, ['view' => $view]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
