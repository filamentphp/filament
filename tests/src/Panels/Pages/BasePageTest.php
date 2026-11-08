<?php

use Filament\Pages\BasePage;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('strips HTML in `getBrowserTitle()` without using `getHeading()`', function (): void {
    $page = new class extends BasePage
    {
        public function getTitle(): string
        {
            return 'Browser: <script>alert(1)</script> & title';
        }

        public function getHeading(): string
        {
            return 'Distinct heading';
        }
    };

    expect($page->getBrowserTitle())->toBe('Browser: alert(1) & title - Laravel');
});
