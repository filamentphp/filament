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
        $payload = Livewire::isLivewireRequest()
            ? (($response instanceof JsonResponse) ? $response->getData() : json_decode($response->getContent() ?: ''))
            : null;
        $isLivewireResponse = is_object($payload) && is_array($payload->components ?? null);
        $batch = ($isLivewireResponse && $mailbox->hasPendingNotifications())
            ? bin2hex(random_bytes(20))
            : null;

        if ($mailbox->hasPendingNotifications()) {
            // Publish before releasing redirects, using the final session scope.
            $mailbox->flush($batch);
        }

        if ($isLivewireResponse) {
            if ($batch !== null) {
                $payload->filamentNotificationBatch = $batch;

                // Streaming may have already sent headers, so retain it in JSON too.
                $response->headers->set('X-Filament-Notifications', $batch);

                if (collect($payload->components)->contains(static fn (object $component): bool => filled($component->effects->redirect ?? null))) {
                    $response->headers->set('X-Filament-Notifications-Redirect', 'true');
                }
            }

            if (($batch !== null) || $mailbox->hasPublishedNotifications()) {
                $payload->filamentNotifications = true;

                if ($response instanceof JsonResponse) {
                    $response->setData($payload);
                } else {
                    $response->setContent(json_encode($payload));
                }
            }
        }

        return $response;
    }
}
