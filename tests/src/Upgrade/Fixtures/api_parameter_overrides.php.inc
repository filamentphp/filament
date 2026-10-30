<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiParameterOverrides;

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

trait Preview
{
    public static function toPreviewHtml(array $config): ?string
    {
        return $config['title'];
    }
}

abstract class Block extends RichContentCustomBlock
{
    use Preview {
        toPreviewHtml as renderPreview;
    }
}

class Banner extends Block
{
    public static function getId(): string
    {
        return 'banner';
    }
}

class Counters extends ImportChunkProcessed
{
    public function counts(): array
    {
        return [$this->processedRows, $this->successfulRows];
    }
}

class IntegerCast extends BooleanStateCast
{
    public function __construct(protected bool $isStoredAsInt = true)
    {
        parent::__construct(isStoredAsInt: $isStoredAsInt);
    }

    public function inspect(?self $other): ?bool
    {
        return $other?->isStoredAsInt;
    }
}

function result(): array
{
    $event = new Counters(new Import, [], [], processedRows: 11, successfulRows: 7);

    return [Banner::renderPreview(config: ['title' => 'Trait']), ...$event->counts(), (new IntegerCast(isStoredAsInt: true))->set(false), (new IntegerCast)->inspect(new IntegerCast(false)), (new IntegerCast)->inspect(null)];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiParameterOverrides;

use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

trait Preview
{
    public static function toPreviewHtml(array $configuration): ?string
    {
        $config =& $configuration;
        return $config['title'];
    }
}

abstract class Block extends RichContentCustomBlock
{
    use Preview {
        toPreviewHtml as renderPreview;
    }
}

class Banner extends Block
{
    public static function getId(): string
    {
        return 'banner';
    }
}

class Counters extends ImportChunkProcessed
{
    public function counts(): array
    {
        return [$this->processedRowsCount, $this->successfulRowsCount];
    }
}

class IntegerCast extends BooleanStateCast
{
    public function __construct(protected bool $isStoredAsInteger = true)
    {
        $isStoredAsInt =& $isStoredAsInteger;
        parent::__construct(isStoredAsInteger: $isStoredAsInt);
    }

    public function inspect(?self $other): ?bool
    {
        return $other?->isStoredAsInteger;
    }
}

function result(): array
{
    $event = new Counters(new Import, [], [], processedRowsCount: 11, successfulRowsCount: 7);

    return [Banner::renderPreview(configuration: ['title' => 'Trait']), ...$event->counts(), (new IntegerCast(isStoredAsInteger: true))->set(false), (new IntegerCast)->inspect(new IntegerCast(false)), (new IntegerCast)->inspect(null)];
}
