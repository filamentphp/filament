<?php

namespace Filament\Forms\Components;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->rule('boolean');
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(BooleanStateCast::class, ['isNullable' => false]),
        ];
    }
}
