<?php

namespace Filament\Notifications;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
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
    use Concerns\CanBeInline;
    use Concerns\HasActions;
    use Concerns\HasBody;
    use Concerns\HasDate;
    use Concerns\HasDuration;
    use Concerns\HasId;
    use Concerns\HasTitle;
    use HasColor;
    use HasIcon;
    use HasIconColor;

    /**
     * @var view-string
     */
    protected string $view = 'filament-notifications::notification';

    protected string $viewIdentifier = 'notification';

    /**
     * @var array<string>
     */
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

    /**
     * @return array<string, mixed>
     */
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
            'color' => $this->getColor(),
            'duration' => $this->getDuration(),
            'icon' => $this->getIcon(),
            'iconColor' => $this->getIconColor(),
            'title' => $this->getTitle(),
            'view' => $this->getView(),
            'viewData' => $this->getViewData(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
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
        $static->color($data['color'] ?? null);
        $static->duration($data['duration'] ?? $static->getDuration());
        $static->icon($data['icon'] ?? null);
        $static->iconColor($data['iconColor'] ?? $static->getIconColor());
        $static->title($data['title'] ?? null);

        return $static;
    }

    protected function isViewSafe(string $view): bool
    {
        return in_array($view, $this->safeViews, strict: true);
    }

    /**
     * @param  string | array<string>  $safeViews
     */
    public function safeViews(string | array $safeViews): static
    {
        $this->safeViews = [
            ...$this->safeViews,
            ...Arr::wrap($safeViews),
        ];

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

    /**
     * @param  Model | Authenticatable | Collection | array<Model | Authenticatable>  $users
     */
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

    /**
     * @param  Model | Authenticatable | Collection | array<Model | Authenticatable>  $users
     */
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

    /**
     * @return array<string, mixed>
     */
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
            'danger' => $this->danger(),
            'info' => $this->info(),
            'success' => $this->success(),
            'warning' => $this->warning(),
            default => $this,
        };
    }

    public function danger(): static
    {
        $this->icon('heroicon-o-x-circle');
        $this->iconColor('danger');

        return $this;
    }

    public function info(): static
    {
        $this->icon('heroicon-o-information-circle');
        $this->iconColor('info');

        return $this;
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

    public static function fromDatabase(DatabaseNotificationModel $notification): static
    {
        /** @phpstan-ignore-next-line */
        $static = static::fromArray($notification->data);
        $static->id($notification->getKey());

        return $static;
    }

    public static function assertNotified(Notification | string | null $notification = null): void
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
