<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralInset;

use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;

function stored(Builder $component, bool | Closure $condition): Builder
{
    return $component->inset(condition: $condition)->label('configured');
}

function nullable(?Repeater $component): ?Repeater
{
    return $component?->inset(false)->label('configured');
}

function factory(): Builder
{
    return Builder::make('blocks')->inset(fn (): bool => true)->label('configured');
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralInset;

use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;

function stored(Builder $component, bool | Closure $condition): Builder
{
    return $component->tap((static fn(bool|Closure $condition): Closure => static fn($component) => $component)($condition))->label('configured');
}

function nullable(?Repeater $component): ?Repeater
{
    return $component?->tap((static fn(bool|Closure $condition): Closure => static fn($component) => $component)(false))->label('configured');
}

function factory(): Builder
{
    return Builder::make('blocks')->tap((static fn(bool|Closure $condition): Closure => static fn($component) => $component)(fn (): bool => true))->label('configured');
}
