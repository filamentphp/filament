<?php

use Filament\Pages\BasePage;
use Filament\Support\Exceptions\Halt;

it('uses `$areFormActionsSticky` for direct configuration and `stickyFormActions()`', function (): void {
    $page = new class extends BasePage
    {
        public static bool $areFormActionsSticky = true;
    };

    expect($page->areFormActionsSticky())->toBeTrue();

    $page::stickyFormActions(false);

    expect($page::$areFormActionsSticky)->toBeFalse()
        ->and($page->areFormActionsSticky())->toBeFalse();

    $page::stickyFormActions();

    expect($page::$areFormActionsSticky)->toBeTrue()
        ->and($page->areFormActionsSticky())->toBeTrue();
});

it('accepts `shouldRollBackDatabaseTransaction` as a named `halt()` argument', function (bool $shouldRollBackDatabaseTransaction): void {
    $page = new class extends BasePage
    {
        public function stop(bool $shouldRollBackDatabaseTransaction): void
        {
            $this->halt(shouldRollBackDatabaseTransaction: $shouldRollBackDatabaseTransaction);
        }
    };

    try {
        $page->stop($shouldRollBackDatabaseTransaction);
    } catch (Halt $exception) {
        expect($exception->shouldRollBackDatabaseTransaction())->toBe($shouldRollBackDatabaseTransaction);

        return;
    }

    $this->fail('Expected `halt()` to throw `Halt`.');
})->with([false, true]);
