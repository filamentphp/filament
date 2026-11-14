<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

use function Amp\delay;

class ControlLoadingIndicatorBrowserTest extends Page
{
    protected string $view = 'pages.control-loading-indicator-browser-test';

    public function slow(): void
    {
        // The browser test server runs in the test's process, so a blocking `usleep()` would also pause the test's assertions.
        delay(1.5);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('slowButton')
                ->icon(Heroicon::Bolt)
                ->extraAttributes(['data-testid' => 'slow-button'])
                ->action(fn () => $this->slow()),
            Action::make('slowIconButton')
                ->iconButton()
                ->icon(Heroicon::Bolt)
                ->extraAttributes(['data-testid' => 'slow-icon-button'])
                ->action(fn () => $this->slow()),
        ];
    }
}
