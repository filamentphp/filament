<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Actions\Imports\Events\ImportChunkProcessed;

class ParameterCollision extends ImportChunkProcessed
{
    protected int $independentCount;

    public function __construct(protected int $processedRows, protected int $successfulRows, int $processedRowsCount = 99)
    {
        $this->independentCount = $processedRowsCount;
    }

    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRows, $this->independentCount];
    }
}

function results(): array
{
    return (new ParameterCollision(processedRows: 10, successfulRows: 7, processedRowsCount: 99))->counts();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiPropertyCollisions;

use Filament\Actions\Imports\Events\ImportChunkProcessed;

class ParameterCollision extends ImportChunkProcessed
{
    protected int $independentCount;

    public function __construct(protected int $processedRows, protected int $successfulRowsCount, int $processedRowsCount = 99)
    {
        $successfulRows =& $successfulRowsCount;
        $this->independentCount = $processedRowsCount;
    }

    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRowsCount, $this->independentCount];
    }
}

function results(): array
{
    return (new ParameterCollision(processedRows: 10, successfulRowsCount: 7, processedRowsCount: 99))->counts();
}
