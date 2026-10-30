<?php

namespace Filament\Tests\Upgrade\Fixtures\ConcealmentAndWidgetFilters;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

interface CustomContract extends CanConcealComponents {}

class Container extends Component implements CanConcealComponents
{
    public function isConcealed(): bool
    {
        return parent::isConcealed() || customCondition();
    }
}

function inspect(Component $component, CanConcealComponents $container): void
{
    $component->isConcealed();
    makeComponent()->isConcealed();
    $component?->isConcealed();
    $callback = $component->isConcealed(...);
    $isContainer = $container instanceof CanConcealComponents;
}

class FilteredWidget extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        return [$this->filters, $this->pageFilters, $this->{'filters'}];
    }
}

class CustomWidget extends FilteredWidget
{
    public array $filters = ['period' => 'custom'];

    public function __get($property): mixed
    {
        return parent::__get($property);
    }
}

class Unrelated
{
    public array $filters = [];

    public function isConcealed(): bool
    {
        return false;
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ConcealmentAndWidgetFilters;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

interface CustomContract extends CanConcealComponents {}

class Container extends Component implements CanConcealComponents
{
    public function isConcealed(): bool
    {
        return (\method_exists(parent::class, 'isConcealed') ? parent::{'isConcealed'}(...) : fn() => false)() || customCondition();
    }
}

function inspect(Component $component, CanConcealComponents $container): void
{
    (static fn($component) => \method_exists($component, 'isConcealed') ? $component->{'isConcealed'}(...) : static fn() => false)($component)();
    makeComponent()->isConcealed();
    ($componentForRemovedGetter1 = $component) === null ? null : (static fn($component) => \method_exists($component, 'isConcealed') ? $component->{'isConcealed'}(...) : static fn() => false)($componentForRemovedGetter1)();
    $callback = (static fn($component) => \method_exists($component, 'isConcealed') ? $component->{'isConcealed'}(...) : static fn() => false)($component);
    $isContainer = $container instanceof CanConcealComponents;
}

class FilteredWidget extends Widget
{
    use InteractsWithPageFilters;

    public function report(): array
    {
        return [$this->filters, $this->pageFilters, $this->{'filters'}];
    }
}

class CustomWidget extends FilteredWidget
{
    public array $filters = ['period' => 'custom'];

    public function __get($property): mixed
    {
        return parent::__get($property);
    }
}

class Unrelated
{
    public array $filters = [];

    public function isConcealed(): bool
    {
        return false;
    }
}
