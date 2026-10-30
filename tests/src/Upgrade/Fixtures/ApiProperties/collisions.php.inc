<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Pages\BasePage;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class DirectCollision extends BooleanStateCast
{
    public bool $isStoredAsInt = true;

    public bool $isStoredAsInteger = false;
}

class LegacyParent extends BooleanStateCast
{
    public bool $isStoredAsInt = true;

    public function inspect(?BooleanStateCast $other): array
    {
        return [$this->isStoredAsInt, $other?->isStoredAsInt];
    }
}

class InheritedCollision extends LegacyParent
{
    public bool $isStoredAsInteger = false;
}

class LegacySibling extends BooleanStateCast
{
    public bool $isStoredAsInt = true;
}

class PromotedCollision extends BooleanStateCast
{
    public bool $isStoredAsInteger = false;

    public function __construct(public bool $isStoredAsInt = true) {}
}

class MixedCollision extends ImportChunkProcessed
{
    protected int $processedRowsCount = 99;

    public function __construct(protected int $processedRows, protected int $successfulRows) {}

    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRows];
    }
}

class StickyParent extends SimplePage
{
    public static bool $formActionsAreSticky = true;
}

class StickyChild extends StickyParent
{
    public static bool $areFormActionsSticky = false;

    public function inspect(): array
    {
        return [parent::$formActionsAreSticky, static::$formActionsAreSticky];
    }
}

function inspect(BooleanStateCast $cast): array
{
    return [$cast->isStoredAsInt, $cast?->isStoredAsInt];
}

function inspectSticky(BasePage $page): bool
{
    return $page::$formActionsAreSticky;
}

function results(): array
{
    $inherited = new InheritedCollision;
    $sticky = new StickyChild;

    return [
        inspect(new DirectCollision),
        $inherited->inspect($inherited),
        $inherited->inspect(null),
        inspect(new LegacySibling),
        inspect(new PromotedCollision(isStoredAsInt: true)),
        $sticky->inspect(),
        inspectSticky($sticky),
        (new MixedCollision(processedRows: 10, successfulRows: 7))->counts(),
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Pages\BasePage;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class DirectCollision extends BooleanStateCast
{
    public bool $isStoredAsInt = true;

    public bool $isStoredAsInteger = false;
}

class LegacyParent extends BooleanStateCast
{
    public bool $isStoredAsInt = true;

    public function inspect(?BooleanStateCast $other): array
    {
        return [$this->isStoredAsInt, $other?->isStoredAsInt];
    }
}

class InheritedCollision extends LegacyParent
{
    public bool $isStoredAsInteger = false;
}

class LegacySibling extends BooleanStateCast
{
    public bool $isStoredAsInt = true;
}

class PromotedCollision extends BooleanStateCast
{
    public bool $isStoredAsInteger = false;

    public function __construct(public bool $isStoredAsInt = true) {}
}

class MixedCollision extends ImportChunkProcessed
{
    protected int $processedRowsCount = 99;

    public function __construct(protected int $processedRows, protected int $successfulRowsCount) {}

    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRowsCount];
    }
}

class StickyParent extends SimplePage
{
    public static bool $formActionsAreSticky = true;
}

class StickyChild extends StickyParent
{
    public static bool $areFormActionsSticky = false;

    public function inspect(): array
    {
        return [parent::$formActionsAreSticky, static::$formActionsAreSticky];
    }
}

function inspect(BooleanStateCast $cast): array
{
    return [$cast->isStoredAsInt, $cast?->isStoredAsInt];
}

function inspectSticky(BasePage $page): bool
{
    return $page::$formActionsAreSticky;
}

function results(): array
{
    $inherited = new InheritedCollision;
    $sticky = new StickyChild;

    return [
        inspect(new DirectCollision),
        $inherited->inspect($inherited),
        $inherited->inspect(null),
        inspect(new LegacySibling),
        inspect(new PromotedCollision(isStoredAsInt: true)),
        $sticky->inspect(),
        inspectSticky($sticky),
        (new MixedCollision(processedRows: 10, successfulRowsCount: 7))->counts(),
    ];
}
