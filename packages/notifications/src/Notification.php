<?php

namespace Filament\Notifications;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\View\Components\NotificationComponent;
use Filament\Notifications\View\Components\NotificationComponent\IconComponent;
use Filament\Notifications\View\NotificationsIconAlias;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Contracts\ScalableIcon;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification as DatabaseNotificationModel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use PHPUnit\Framework\Assert;

use function Filament\Support\generate_icon_html;

class Notification extends ViewComponent implements Arrayable, HasEmbeddedView
{
    use Concerns\CanBeInline;
    use Concerns\HasActions;
    use Concerns\HasBody;
    use Concerns\HasDate;
    use Concerns\HasDuration;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use Concerns\HasId;
    use Concerns\HasStatus;
    use Concerns\HasTitle;
    use HasColor;
    use HasIconSize;

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

    public function toArray(): array
    {
        $icon = $this->getIcon();

        if ($icon instanceof ScalableIcon) {
            $icon = $icon->getIconForSize(IconSize::Large);
        } elseif ($icon instanceof BackedEnum) {
            $icon = $icon->value;
        }

        return [
            'id' => $this->getId(),
            'actions' => array_map(fn (Action | ActionGroup $action): array => $action->toArray(), $this->getActions()),
            'body' => $this->getBody(),
            'color' => $this->getColor(),
            'duration' => $this->getDuration(),
            'icon' => $icon,
            'iconColor' => $this->getIconColor(),
            'status' => $this->getStatus(),
            'title' => $this->getTitle(),
            'view' => $this->hasView() ? $this->getView() : null,
            'viewData' => $this->getViewData(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $static = static::make($data['id'] ?? Str::random());

        // If the container constructs an instance of child class
        // instead of the current class, we should run `fromArray()`
        // on the child class instead.
        if (
            ($static::class !== self::class) &&
            (get_called_class() === self::class)
        ) {
            return $static::fromArray($data);
        }

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

        if (filled($view) && ((! $static->hasView()) || ($static->getView() !== $view)) && $static->isViewSafe($view)) {
            $static->view($data['view']);
        }

        $static->viewData($data['viewData'] ?? []);
        $static->body($data['body'] ?? null);
        $static->color($data['color'] ?? null);
        $static->duration($data['duration'] ?? $static->getDuration());
        $static->status($data['status'] ?? $static->getStatus());
        $static->icon($data['icon'] ?? $static->getIcon());
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

        return app(BroadcastNotification::class, ['data' => $data]);
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

    public static function fromDatabase(DatabaseNotificationModel $notification): static
    {
        /** @phpstan-ignore-next-line */
        $static = static::fromArray($notification->data);
        $static->id($notification->getKey());

        return $static;
    }

    public static function assertNotified(Notification | string | null $notification = null): void
    {
        $notificationsLivewireComponent = new Notifications;
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

    public static function assertNotNotified(Notification | string | null $notification = null): void
    {
        $notificationsLivewireComponent = new Notifications;
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

        if ($notification instanceof Notification) {
            Assert::assertNotSame(
                collect($expectedNotification)->except(['id'])->toArray(),
                collect($notification->toArray())->except(['id'])->toArray(),
                'The notification with the given configuration was sent'
            );

            return;
        }

        if ($expectedNotification instanceof Notification) {
            Assert::assertNotSame(
                $expectedNotification->title,
                $notification,
                'The notification with the given title was sent'
            );
        }
    }

    public function toEmbeddedHtml(): string
    {
        $status = $this->getStatus();
        $title = $this->getTitle();
        $hasTitle = filled($title);
        $date = $this->getDate();
        $hasDate = filled($date);
        $body = $this->getBody();
        $hasBody = filled($body);

        $attributes = (new ComponentAttributeBag)
            ->merge([
                'wire:key' => "{$this->getId()}.notifications.{$this->getId()}",
                'x-on:close-notification.window' => "if (\$event.detail.id == '{$this->getId()}') close()",
            ], escape: false)
            ->color(NotificationComponent::class, $this->getColor() ?? 'gray')
            ->class([
                'fi-no-notification',
                'fi-inline' => $this->isInline,
                "fi-status-{$status}" => $status,
            ]);

        ob_start(); ?>

        <div
            x-data="notificationComponent({ notification: <?= Js::from($this->toArray()) ?> })"
            x-transition:enter-start="fi-transition-enter-start"
            x-transition:enter-end="fi-transition-enter-end"
            x-transition:leave-start="fi-transition-leave-start"
            x-transition:leave-end="fi-transition-leave-end"
            <?= $attributes ?>
        >
            <?= generate_icon_html(
                $this->getIcon(),
                attributes: (new ComponentAttributeBag)->color(IconComponent::class, $this->getIconColor())->class(['fi-no-notification-icon']),
                size: $this->getIconSize(),
            )?->toHtml() ?>

            <div class="fi-no-notification-main">
                <?php if ($hasTitle || $hasDate || $hasBody) { ?>
                    <div class="fi-no-notification-text">
                        <?php if ($hasTitle) { ?>
                            <h3 class="fi-no-notification-title">
                                <?= str($title)->sanitizeHtml() ?>
                            </h3>
                        <?php } ?>

                        <?php if ($hasDate) { ?>
                            <time class="fi-no-notification-date">
                                <?= e($date) ?>
                            </time>
                        <?php } ?>

                        <?php if ($hasBody) { ?>
                            <div class="fi-no-notification-body">
                                <?= str($body)->sanitizeHtml() ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($actions = $this->getActions()) { ?>
                    <div class="fi-ac fi-no-notification-actions">
                        <?php foreach ($actions as $action) { ?>
                            <?= $action->toHtml() ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <button
                type="button"
                x-on:click="close"
                class="fi-icon-btn fi-no-notification-close-btn"
            >
                <?= generate_icon_html(Heroicon::XMark, alias: NotificationsIconAlias::NOTIFICATION_CLOSE_BUTTON)->toHtml() ?>
            </button>
        </div>

        <?php return ob_get_clean();
    }
}
