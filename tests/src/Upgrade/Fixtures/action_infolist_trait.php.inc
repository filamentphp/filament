<?php

namespace Filament\Tests\Upgrade\Fixtures\ActionInfolistTrait;

use Filament\Actions\Action;
use Filament\Actions\Concerns\HasInfolist;
use Filament\Schemas\Components\Text;

class CustomAction extends Action
{
    use HasInfolist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->infolist(infolist: [Text::make('Details')]);
    }
}

class CustomNames
{
    public function infolist(string $infolist): string
    {
        return $infolist;
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ActionInfolistTrait;

use Filament\Actions\Concerns\HasSchema;
use Filament\Actions\Action;
use Filament\Actions\Concerns\HasInfolist;
use Filament\Schemas\Components\Text;

class CustomAction extends Action
{
    use HasSchema;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(schema: [Text::make('Details')]);
    }
}

class CustomNames
{
    public function infolist(string $infolist): string
    {
        return $infolist;
    }
}
