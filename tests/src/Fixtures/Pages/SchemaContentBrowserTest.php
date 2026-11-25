<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class SchemaContentBrowserTest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            static fn (): string => '<svg onload="window.schemaClosureMarker = true"><title>Stored closure & text</title></svg>',
            str('<svg onload="window.schemaStringableMarker = true"><title>Stored stringable & text</title></svg>'),
            new HtmlString('<strong data-testid="trusted-schema-content">Intentional HTML</strong>'),
        ]);
    }
}
