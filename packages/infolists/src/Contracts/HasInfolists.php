<?php

namespace Filament\Infolists\Contracts;

use Filament\Infolists\Infolist;

interface HasInfolists
{
    public function getInfolist(string $name): ?Infolist;
}
