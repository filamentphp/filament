<?php

namespace Filament\Tests\Fixtures\Policies;

use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    public function viewAny(User $user): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::viewAny', fn (): bool => true);

        return app(TicketPolicy::class . '::viewAny');
    }

    public function view(User $user, Ticket $ticket): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::view', fn (): bool => true);

        return app(TicketPolicy::class . '::view');
    }

    public function create(User $user): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::create', fn (): bool => true);

        return app(TicketPolicy::class . '::create');
    }

    public function update(User $user, Ticket $ticket): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::update', fn (): bool => true);

        return app(TicketPolicy::class . '::update');
    }

    public function delete(User $user, Ticket $ticket): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::delete', fn (): bool => true);

        return app(TicketPolicy::class . '::delete');
    }

    public function deleteAny(User $user): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::deleteAny', fn (): bool => true);

        return app(TicketPolicy::class . '::deleteAny');
    }

    public function restore(User $user, Ticket $ticket): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::restore', fn (): bool => true);

        return app(TicketPolicy::class . '::restore');
    }

    public function restoreAny(User $user): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::restoreAny', fn (): bool => true);

        return app(TicketPolicy::class . '::restoreAny');
    }

    public function forceDelete(User $user, Ticket $ticket): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::forceDelete', fn (): bool => true);

        return app(TicketPolicy::class . '::forceDelete');
    }

    public function forceDeleteAny(User $user): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::forceDeleteAny', fn (): bool => true);

        return app(TicketPolicy::class . '::forceDeleteAny');
    }

    public function replicate(User $user, Ticket $ticket): bool | Response
    {
        app()->bindIf(TicketPolicy::class . '::replicate', fn (): bool => true);

        return app(TicketPolicy::class . '::replicate');
    }
}
