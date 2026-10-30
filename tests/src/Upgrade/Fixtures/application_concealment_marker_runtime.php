<?php

namespace Filament\Tests\Upgrade\Fixtures\ApplicationConcealmentMarker;

use Filament\Schemas\Components\Component;
use RuntimeException;

// Application-owned replacement for apps which actually identify containers.
interface CanConcealComponents {}

interface CustomContract extends CanConcealComponents {}

class Container extends Component implements CustomContract {}

function identify(CanConcealComponents $container): bool
{
    return $container instanceof CanConcealComponents;
}

if (! identify(new Container)) {
    throw new RuntimeException('Application marker identification changed');
}

echo "application marker type contract preserved\n";
