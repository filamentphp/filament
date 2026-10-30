<?php

namespace Filament\Tests\Upgrade\Fixtures\ConcealmentMarkerContracts;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;

interface CustomContract extends CanConcealComponents {}

class Container extends Component implements CanConcealComponents {}

function identify(CanConcealComponents $container): bool
{
    return $container instanceof CanConcealComponents;
}

function dynamic(Component $component, object $unrelated, string $method): void
{
    $component->{$method}();
    $unrelated->isConcealed();
    $unrelated->getExample();
}
