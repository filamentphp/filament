<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class IconBrowserTest extends Page
{
    protected string $view = 'pages.icon-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCheckCircle;

    public function content(Schema $schema): Schema
    {
        $imagePathIcon = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" /></svg>');

        return $schema->components([
            Icon::make(Heroicon::Check)
                ->extraAttributes([
                    'aria-label' => 'Verified account',
                    'data-testid' => 'built-in-named',
                ]),
            Icon::make(new HtmlString('<svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" /></svg>'))
                ->extraAttributes([
                    'aria-label' => 'Custom status',
                    'data-testid' => 'custom-named',
                ]),
            Icon::make(new HtmlString('<svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" /></svg>'))
                ->tooltip('Custom warning')
                ->extraAttributes(['data-testid' => 'custom-tooltip']),
            Icon::make(Heroicon::Check)
                ->extraAttributes(['data-testid' => 'built-in-decorative']),
            Icon::make(new HtmlString('<svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" /></svg>'))
                ->extraAttributes(['data-testid' => 'custom-decorative']),
            Icon::make($imagePathIcon)
                ->extraAttributes([
                    'aria-label' => 'Path status',
                    'data-testid' => 'path-named',
                ]),
            Icon::make($imagePathIcon)
                ->extraAttributes(['data-testid' => 'path-decorative']),
        ]);
    }
}
