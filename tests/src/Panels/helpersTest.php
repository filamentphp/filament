<?php

use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\TestCase;

use function Filament\get_authorization_response;

uses(TestCase::class);

it('returns the same `FilamentManager` instance from `filament()`', function (): void {
    expect(filament())
        ->toBeInstanceOf(FilamentManager::class)
        ->toBe(filament());
});

it('rejects a missing policy method in strict mode with `get_authorization_response()`', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    get_authorization_response('edit', Ticket::class);
})->throws(Exception::class, 'Strict authorization mode is enabled, but no [edit()] method was found on [Filament\Tests\Fixtures\Policies\TicketPolicy].');
