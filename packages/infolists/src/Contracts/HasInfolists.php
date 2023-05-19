<?php

namespace Filament\Infolists\Contracts;

use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Infolist;

interface HasInfolists extends HasForms
{
    public function getInfolist(string $name): ?Infolist;
}
