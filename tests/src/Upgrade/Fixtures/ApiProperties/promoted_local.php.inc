<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class LocalCollision extends ImportChunkProcessed
{
    protected int $independentCount;

    public function __construct(protected int $processedRows, protected int $successfulRows)
    {
        $processedRowsCount = 99;
        $this->independentCount = $processedRowsCount;
    }

    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRows, $this->independentCount];
    }
}

class LocalCast extends BooleanStateCast
{
    public bool $independentValue;

    public function __construct(bool $isStoredAsInt = true)
    {
        $isStoredAsInteger = ! $isStoredAsInt;
        $this->independentValue = $isStoredAsInteger;
    }
}

class PromotedCast extends BooleanStateCast
{
    public function __construct(protected bool $isStoredAsInt = true) {}

    public function stored(): bool
    {
        return $this->isStoredAsInt;
    }
}

function results(): array
{
    return [
        ...(new LocalCollision(processedRows: 10, successfulRows: 7))->counts(),
        (new PromotedCast(isStoredAsInt: true))->stored(),
        (new LocalCast(isStoredAsInt: true))->independentValue,
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

class LocalCollision extends ImportChunkProcessed
{
    protected int $independentCount;

    public function __construct(protected int $processedRows, protected int $successfulRowsCount)
    {
        $successfulRows =& $successfulRowsCount;
        $processedRowsCount = 99;
        $this->independentCount = $processedRowsCount;
    }

    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRowsCount, $this->independentCount];
    }
}

class LocalCast extends BooleanStateCast
{
    public bool $independentValue;

    public function __construct(bool $isStoredAsInt = true)
    {
        $isStoredAsInteger = ! $isStoredAsInt;
        $this->independentValue = $isStoredAsInteger;
    }
}

class PromotedCast extends BooleanStateCast
{
    public function __construct(protected bool $isStoredAsInt = true) {}

    public function stored(): bool
    {
        return $this->isStoredAsInt;
    }
}

function results(): array
{
    return [
        ...(new LocalCollision(processedRows: 10, successfulRowsCount: 7))->counts(),
        (new PromotedCast(isStoredAsInt: true))->stored(),
        (new LocalCast(isStoredAsInt: true))->independentValue,
    ];
}
