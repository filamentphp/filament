<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class BaseSchemaCachingBrowserTest extends Page
{
    protected string $view = 'pages.schema-caching-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public function extensionSchema(): Schema
    {
        return Schema::make($this)
            ->components([
                Text::make('Customized no-argument schema'),
            ]);
    }

    public function defaultExtension(Schema $schema): Schema
    {
        return $schema->extraAttributes([
            'data-default-hook' => 'base',
        ]);
    }
}

class SchemaCachingBrowserTest extends BaseSchemaCachingBrowserTest
{
    public function defaultExtension(Schema $schema): Schema
    {
        return parent::defaultExtension($schema)
            ->extraAttributes([
                'data-testid' => 'customized-no-argument-schema',
                'data-subclass-hook' => 'applied',
            ], merge: true);
    }
}
