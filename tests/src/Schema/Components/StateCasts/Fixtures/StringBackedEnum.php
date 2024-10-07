<?php

namespace Filament\Tests\Schema\Components\StateCasts\Fixtures;

enum StringBackedEnum: string
{
    case One = 'one';
    case Two = 'two';
    case Three = 'three';
}
