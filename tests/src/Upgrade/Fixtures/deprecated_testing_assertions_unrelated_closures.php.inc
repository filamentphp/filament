<?php

namespace Filament\Tests\Upgrade\Fixtures\UnrelatedTestingClosures;

use Closure;
use Livewire\Features\SupportTesting\Testable;

class OtherAssertions
{
    public function assertFormExists(): void {}
}

class NullableTestable extends Testable
{
    public ?self $optional = null;

    public function maybe(): ?self
    {
        return null;
    }
}

function nullableIntermediates(?NullableTestable $testable): void
{
    $testable?->maybe()->assertFormExists();
    $testable?->optional->assertFormExists();
}

function assertOnAnotherObject(Testable $testable): void
{
    (static fn (Testable $testable) => false
        ? null
        : Closure::bind(
            function () { return new OtherAssertions; },
            $testable,
            Testable::class,
        )
    )($testable)->__invoke()->assertFormExists();
}
