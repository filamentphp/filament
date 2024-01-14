<?php

namespace Filament\Forms\Components;

use Filament\Support\Concerns\HasExtraAlpineAttributes;

class Toggle extends Field
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasToggleColors;
    use Concerns\HasToggleIcons;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.toggle';

    protected string $evaluationIdentifier = 'toggle';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->afterStateHydrated(static function (Toggle $component, $state): void {
            $component->state((bool) $state);
        });

        $this->rule('boolean');
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'field', 'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
