<?php

namespace Filament\Notifications;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Concerns\CanBeInline;
use Filament\Notifications\Concerns\HasActions;
use Filament\Notifications\Concerns\HasBody;
use Filament\Notifications\Concerns\HasDate;
use Filament\Notifications\Concerns\HasDuration;
use Filament\Notifications\Concerns\HasIcon;
use Filament\Notifications\Concerns\HasId;
use Filament\Notifications\Concerns\HasTitle;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Http\Livewire\Notifications;
use Filament\Support\Components\ViewComponent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification as DatabaseNotificationModel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;

class Notification extends ViewComponent implements Arrayable
{
    use CanBeInline;
    use HasActions;
    use HasBody;
    use HasDate;
    use HasDuration;
    use HasIcon;
    use HasId;
    use HasTitle;

    protected string $view = 'notifications::notification';

    protected string $viewIdentifier = 'notification';

    protected array $safeViews = [];

    public function __construct(string $id)
    {
        $this->id($id);
    }

    public static function make(?string $id = null): static
    {
        $static = app(static::class, ['id' => $id ?? Str::orderedUuid()]);
        $static->configure();

        return $static;
    }

    public function getViewData(): array
    {
        return $this->viewData;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'actions' => array_map(fn (Action | ActionGroup $action): array => $action->toArray(), $this->getActions()),
            'body' => $this->getBody(),
            'duration' => $this->getDuration(),
            'icon' => $this->getIcon(),
            'iconColor' => $this->getIconColor(),
            'title' => $this->getTitle(),
            'view' => $this->getView(),
            'viewData' => $this->getViewData(),
        ];
    }

    public static function fromArray(array $data): static
    {
        $static = static::make($data['id'] ?? Str::random());
        $static->actions(
            array_map(
                fn (array $action): Action | ActionGroup => match (array_key_exists('actions', $action)) {
                    true => ActionGroup::fromArray($action),
                    false => Action::fromArray($action),
                },
                $data['actions'] ?? [],
            ),
        );

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && $static->isViewSafe($view)) {
            $static->view($data['view']);
        }

        $static->viewData($data['viewData'] ?? []);
        $static->body($data['body'] ?? null);
        $static->duration($data['duration'] ?? null);
        $static->icon($data['icon'] ?? null);
        $static->iconColor($data['iconColor'] ?? $static->getIconColor());
        $static->title($data['title'] ?? null);

        return $static;
    }

    protected function isViewSafe(string $view): bool
    {
        return in_array($view, $this->safeViews, strict: true);
    }

    public function safeViews(string | array $safeViews): static
    {
        $this->safeViews = array_merge(
            $this->safeViews,
            Arr::wrap($safeViews),
        );

        return $this;
    }

    public function send(): static
    {
        session()->push(
            'filament.notifications',
            $this->toArray(),
        );

        return $this;
    }

    public function broadcast(Model | Authenticatable | Collection | array $users): static
    {
        if (! is_iterable($users)) {
            $users = [$users];
        }

        foreach ($users as $user) {
            $user->notify($this->toBroadcast());
        }

        return $this;
    }

    public function sendToDatabase(Model | Authenticatable | Collection | array $users, bool $isEventDispatched = false): static
    {
        if (! is_iterable($users)) {
            $users = [$users];
        }

        foreach ($users as $user) {
            $user->notify($this->toDatabase());

            if ($isEventDispatched) {
                DatabaseNotificationsSent::dispatch($user);
            }
        }

        return $this;
    }

    public function toBroadcast(): BroadcastNotification
    {
        $data = $this->toArray();
        $data['format'] = 'filament';

        return new BroadcastNotification($data);
    }

    public function toDatabase(): DatabaseNotification
    {
        return new DatabaseNotification($this->getDatabaseMessage());
    }

    public function getBroadcastMessage(): BroadcastMessage
    {
        $data = $this->toArray();
        $data['format'] = 'filament';

        return new BroadcastMessage($data);
    }

    public function getDatabaseMessage(): array
    {
        $data = $this->toArray();
        $data['duration'] = 'persistent';
        $data['format'] = 'filament';
        unset($data['id']);

        return $data;
    }

    public function status(string $status): static
    {
        return match ($status) {
            'success' => $this->success(),
            'warning' => $this->warning(),
            'danger' => $this->danger(),
            default => $this,
        };
    }

    public function success(): static
    {
        $this->icon('heroicon-o-check-circle');
        $this->iconColor('success');

        return $this;
    }

    public function warning(): static
    {
        $this->icon('heroicon-o-exclamation-circle');
        $this->iconColor('warning');

        return $this;
    }

    public function danger(): static
    {
        $this->icon('heroicon-o-x-circle');
        $this->iconColor('danger');

        return $this;
    }

    public static function fromDatabase(DatabaseNotificationModel $notification): static
    {
        /** @phpstan-ignore-next-line */
        $static = static::fromArray($notification->data);
        $static->id($notification->getKey());

        return $static;
    }

    public static function assertNotified(Notification | string $notification = null): void
    {
        $notificationsLivewireComponent = new Notifications();
        $notificationsLivewireComponent->mount();
        $notifications = $notificationsLivewireComponent->notifications;

        $expectedNotification = null;

        Assert::assertIsArray($notifications->toArray());

        if (is_string($notification)) {
            $expectedNotification = $notifications->first(fn (Notification $mountedNotification): bool => $mountedNotification->title === $notification);
        }

        if ($notification instanceof Notification) {
            $expectedNotification = $notifications->first(fn (Notification $mountedNotification, string $key): bool => $mountedNotification->id === $key);
        }

        if (blank($notification)) {
            return;
        }

        Assert::assertNotNull($expectedNotification, 'A notification was not sent');

        if ($notification instanceof Notification) {
            Assert::assertSame(
                collect($expectedNotification)->except(['id'])->toArray(),
                collect($notification->toArray())->except(['id'])->toArray()
            );

            return;
        }

        Assert::assertSame($expectedNotification->title, $notification);
    }
}
