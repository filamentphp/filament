<?php

namespace Filament\Notifications\Http\Middleware;

use Closure;
use Filament\Notifications\NotificationMailbox;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

class PublishNotifications
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $mailbox = app(NotificationMailbox::class);

        if ($mailbox->hasPendingNotifications()) {
            // Publish before releasing redirects, using the final session scope.
            $mailbox->flush();
        }

        if (Livewire::isLivewireRequest() && ($response instanceof JsonResponse)) {
            $payload = $response->getData();

            if (
                is_object($payload) &&
                is_array($payload->components ?? null) &&
                $mailbox->hasPublishedNotifications()
            ) {
                $payload->filamentNotifications = true;

                $response->setData($payload);
            }
        }

        return $response;
    }
}
