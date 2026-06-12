<?php

namespace Filament\Actions\Concerns;

use Closure;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\Response;

trait CanNotify
{
    protected Notification | Closure | null $failureNotification = null;

    protected bool $isFailureNotificationDisabled = false;

    protected Notification | Closure | null $successNotification = null;

    protected bool $isSuccessNotificationDisabled = false;

    protected Notification | Closure | null $unauthorizedNotification = null;

    protected bool $isUnauthorizedNotificationDisabled = false;

    protected Notification | Closure | null $rateLimitedNotification = null;

    protected bool $isRateLimitedNotificationDisabled = false;

    protected string | Closure | null $failureNotificationTitle = null;

    protected string | Closure | null $successNotificationTitle = null;

    protected string | Closure | null $unauthorizedNotificationTitle = null;

    protected string | Closure | null $rateLimitedNotificationTitle = null;

    protected string | Closure | null $failureNotificationBody = null;

    protected string | Closure | null $missingBulkAuthorizationFailureNotificationMessage = null;

    protected string | Closure | null $missingBulkProcessingFailureNotificationMessage = null;

    public function missingBulkAuthorizationFailureNotificationMessage(string | Closure | null $message): static
    {
        $this->missingBulkAuthorizationFailureNotificationMessage = $message;

        return $this;
    }

    public function missingBulkProcessingFailureNotificationMessage(string | Closure | null $message): static
    {
        $this->missingBulkProcessingFailureNotificationMessage = $message;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFailureNotificationNamedInjections(): array
    {
        $processingFailureMessages = $this->getBulkProcessingFailureMessages();

        return [
            'authorizationFailureMessages' => $this->bulkAuthorizationFailureMessages,
            'failureCount' => $this->totalSelectedRecordsCount - $this->successfulSelectedRecordsCount,
            'failureMessages' => [...$this->bulkAuthorizationFailureMessages, ...$processingFailureMessages],
            'isAll' => ! $this->successfulSelectedRecordsCount,
            'missingAuthorizationFailureMessageCount' => $this->bulkAuthorizationFailureWithoutMessageCount,
            'missingProcessingFailureMessageCount' => $this->bulkProcessingFailureWithoutMessageCount,
            'processingFailureMessages' => $processingFailureMessages,
            'successCount' => $this->successfulSelectedRecordsCount,
            'totalCount' => $this->totalSelectedRecordsCount,
        ];
    }

    public function sendFailureNotification(): static
    {
        if ($this->isFailureNotificationDisabled) {
            return $this;
        }

        $notification = $this->evaluate($this->failureNotification, [
            ...$this->getFailureNotificationNamedInjections(),
            'notification' => $notification = Notification::make()
                ->when(
                    $this->successfulSelectedRecordsCount,
                    fn (Notification $notification) => $notification->warning(),
                    fn (Notification $notification) => $notification->danger(),
                )
                ->title($this->getFailureNotificationTitle())
                ->body($this->getFailureNotificationBody())
                ->persistent(),
        ]) ?? $notification;

        if (filled($notification?->getTitle())) {
            $notification->send();
        }

        return $this;
    }

    public function failureNotification(Notification | Closure | null $notification): static
    {
        $this->failureNotification = $notification;
        $this->isFailureNotificationDisabled = $notification === null;

        return $this;
    }

    /**
     * @deprecated Use `failureNotificationTitle()` instead.
     */
    public function failureNotificationMessage(string | Closure | null $message): static
    {
        return $this->failureNotificationTitle($message);
    }

    public function failureNotificationTitle(string | Closure | null $title): static
    {
        $this->failureNotificationTitle = $title;

        return $this;
    }

    public function failureNotificationBody(string | Closure | null $body): static
    {
        $this->failureNotificationBody = $body;

        return $this;
    }

    public function sendSuccessNotification(): static
    {
        if ($this->isSuccessNotificationDisabled) {
            return $this;
        }

        $notification = $this->evaluate($this->successNotification, [
            'notification' => $notification = Notification::make()
                ->success()
                ->title($this->getSuccessNotificationTitle()),
        ]) ?? $notification;

        if (filled($notification?->getTitle())) {
            $notification->send();
        }

        return $this;
    }

    public function successNotification(Notification | Closure | null $notification): static
    {
        $this->successNotification = $notification;
        $this->isSuccessNotificationDisabled = $notification === null;

        return $this;
    }

    /**
     * @deprecated Use `successNotificationTitle()` instead.
     */
    public function successNotificationMessage(string | Closure | null $message): static
    {
        return $this->successNotificationTitle($message);
    }

    public function successNotificationTitle(string | Closure | null $title): static
    {
        $this->successNotificationTitle = $title;

        return $this;
    }

    public function sendUnauthorizedNotification(Response $response): static
    {
        if ($this->isUnauthorizedNotificationDisabled) {
            return $this;
        }

        $notification = $this->evaluate($this->unauthorizedNotification, [
            'notification' => $notification = Notification::make()
                ->danger()
                ->title($this->getUnauthorizedNotificationTitle($response) ?? $response->message())
                ->persistent(),
            'response' => $response,
        ]) ?? $notification;

        if (filled($notification?->getTitle())) {
            $notification->send();
        }

        return $this;
    }

    public function unauthorizedNotification(Notification | Closure | null $notification): static
    {
        $this->unauthorizedNotification = $notification;
        $this->isUnauthorizedNotificationDisabled = $notification === null;

        return $this;
    }

    public function unauthorizedNotificationTitle(string | Closure | null $title): static
    {
        $this->unauthorizedNotificationTitle = $title;

        return $this;
    }

    public function sendRateLimitedNotification(TooManyRequestsException $exception): static
    {
        if ($this->isRateLimitedNotificationDisabled) {
            return $this;
        }

        $notification = $this->evaluate($this->rateLimitedNotification, [
            'exception' => $exception,
            'minutes' => $exception->minutesUntilAvailable,
            'notification' => $notification = Notification::make()
                ->danger()
                ->title($this->getRateLimitedNotificationTitle($exception) ?? __('filament-actions::notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ]))
                ->body(__('filament-actions::notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ])),
            'seconds' => $exception->secondsUntilAvailable,
        ]) ?? $notification;

        $notification->send();

        return $this;
    }

    public function rateLimitedNotification(Notification | Closure | null $notification): static
    {
        $this->rateLimitedNotification = $notification;
        $this->isRateLimitedNotificationDisabled = $notification === null;

        return $this;
    }

    public function rateLimitedNotificationTitle(string | Closure | null $title): static
    {
        $this->rateLimitedNotificationTitle = $title;

        return $this;
    }

    public function getSuccessNotificationTitle(): ?string
    {
        return $this->evaluate($this->successNotificationTitle);
    }

    public function getFailureNotificationTitle(): ?string
    {
        return $this->evaluate($this->failureNotificationTitle, $this->getFailureNotificationNamedInjections());
    }

    public function getFailureNotificationBody(): ?string
    {
        $body = $this->evaluate($this->failureNotificationBody, $this->getFailureNotificationNamedInjections());

        if (filled($body)) {
            return $body;
        }

        $messages = [
            ...$this->bulkAuthorizationFailureMessages,
            ...($this->bulkAuthorizationFailureWithoutMessageCount && filled($message = $this->evaluate(
                $this->missingBulkAuthorizationFailureNotificationMessage,
                [
                    'count' => $this->bulkAuthorizationFailureWithoutMessageCount,
                    'failureCount' => $this->bulkAuthorizationFailureWithoutMessageCount,
                    'isAll' => $this->bulkAuthorizationFailureWithoutMessageCount === $this->totalSelectedRecordsCount,
                    'total' => $this->totalSelectedRecordsCount,
                    'totalCount' => $this->totalSelectedRecordsCount,
                ],
            )) ? [$message] : []),
            ...$this->getBulkProcessingFailureMessages(),
            ...($this->bulkProcessingFailureWithoutMessageCount && filled($message = $this->evaluate(
                $this->missingBulkProcessingFailureNotificationMessage,
                [
                    'count' => $this->bulkProcessingFailureWithoutMessageCount,
                    'failureCount' => $this->bulkProcessingFailureWithoutMessageCount,
                    'isAll' => $this->bulkProcessingFailureWithoutMessageCount === $this->totalSelectedRecordsCount,
                    'total' => $this->totalSelectedRecordsCount,
                    'totalCount' => $this->totalSelectedRecordsCount,
                ],
            )) ? [$message] : []),
        ];

        return implode(
            '',
            array_map(
                fn (string $message): string => "<p>{$message}</p>",
                $messages,
            ),
        );
    }

    public function getUnauthorizedNotificationTitle(Response $response): ?string
    {
        return $this->evaluate($this->unauthorizedNotificationTitle, [
            'response' => $response,
        ]);
    }

    public function getRateLimitedNotificationTitle(TooManyRequestsException $exception): ?string
    {
        return $this->evaluate($this->rateLimitedNotificationTitle, [
            'exception' => $exception,
            'minutes' => $exception->minutesUntilAvailable,
            'seconds' => $exception->secondsUntilAvailable,
        ]);
    }
}
