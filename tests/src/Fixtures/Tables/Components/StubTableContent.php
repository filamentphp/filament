<?php

namespace Filament\Tests\Fixtures\Tables\Components;

use Filament\Tables\Components\TableContent;

class StubTableContent extends TableContent
{
    public function toEmbeddedHtml(): string
    {
        return '<p>stub content</p>';
    }
}
