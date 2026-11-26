<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());
});

it('does not send another error notification when the error notification request fails', function (): void {
    $page = visit(TicketResource::getUrl('create', panel: 'spa'))
        ->assertPresent('[data-testid="create-ticket"]');

    // The network drops for the save request and the error notification request that follows it, then comes back.
    $page->script(<<<'JS'
        () => {
            const fetch = window.fetch
            let failedLivewireRequests = 0

            window.fetch = (resource, options) => {
                if (options?.headers?.['X-Livewire'] === undefined || failedLivewireRequests === 2) {
                    return fetch(resource, options)
                }

                failedLivewireRequests++

                if (failedLivewireRequests === 2) {
                    setTimeout(() => new FilamentNotification().title('Network restored').send())
                }

                return Promise.reject(new TypeError('Failed to fetch'))
            }
        }
        JS);

    $page->click('[data-testid="create-ticket"]')
        ->assertSeeIn('.fi-no', 'Network restored')
        ->assertScript('document.querySelectorAll(".fi-no-notification").length', 1);
});
