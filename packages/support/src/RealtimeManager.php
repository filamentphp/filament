<?php

namespace Filament\Support;

use BackedEnum;
use Filament\Support\Events\RecordChanged;
use Illuminate\Database\Eloquent\Model;

class RealtimeManager
{
    /**
     * @var array<ModelSubscription>
     */
    protected array $subscriptions = [];

    /**
     * @param  class-string<Model> | ModelSubscription | null  $model
     */
    public function subscribe(string | ModelSubscription | null $model = null): ModelSubscription
    {
        $subscription = ($model instanceof ModelSubscription) ? clone $model : ModelSubscription::make($model);

        $this->subscriptions[] = $subscription;
        $subscription->afterModelSelectorChanged(fn (): array => $this->resolveSubscriptions());
        $this->resolveSubscriptions();

        return $subscription;
    }

    /**
     * @param  class-string<Model> | ModelSubscription  $subscription
     */
    public function unsubscribe(string | ModelSubscription $subscription): static
    {
        $key = ($subscription instanceof ModelSubscription) ? $subscription->getKey() : $subscription;

        foreach ($this->subscriptions as $index => $registeredSubscription) {
            if (($registeredSubscription === $subscription) || (($key !== null) && ($registeredSubscription->getKey() === $key))) {
                $registeredSubscription->afterModelSelectorChanged(null);

                unset($this->subscriptions[$index]);
            }
        }

        return $this;
    }

    /**
     * @return array<string, ModelSubscription>
     */
    public function getSubscriptions(): array
    {
        return array_map(static fn (ModelSubscription $subscription): ModelSubscription => clone $subscription, $this->resolveSubscriptions());
    }

    /**
     * @return array<string, ModelSubscription>
     */
    protected function resolveSubscriptions(): array
    {
        $subscriptionIndexes = [];

        foreach ($this->subscriptions as $index => $subscription) {
            if (($key = $subscription->getKey()) === null) {
                continue;
            }

            if (isset($subscriptionIndexes[$key])) {
                $this->subscriptions[$subscriptionIndexes[$key]]->afterModelSelectorChanged(null);

                unset($this->subscriptions[$subscriptionIndexes[$key]]);
            }

            $subscriptionIndexes[$key] = $index;
        }

        return array_map(fn (int $index): ModelSubscription => $this->subscriptions[$index], $subscriptionIndexes);
    }

    /**
     * @param  class-string<Model>  $model
     */
    public function getSubscription(string $model): ?ModelSubscription
    {
        $subscription = null;

        foreach ($this->resolveSubscriptions() as $candidate) {
            if (! $candidate->matchesModel($model)) {
                continue;
            }

            if ($candidate->getModel() !== null) {
                $subscription = $candidate;

                break;
            }

            if ($subscription && (strlen($candidate->getModelNamespace() ?? '') <= strlen($subscription->getModelNamespace() ?? ''))) {
                continue;
            }

            $subscription = $candidate;
        }

        return $subscription ? clone $subscription : null;
    }

    public function handleModelEvent(string $event, Model $record): void
    {
        $subscription = $this->getSubscription($record::class);

        if (! $subscription) {
            return;
        }

        foreach ($subscription->getEvents() as $subscribedEvent) {
            $eventName = ($subscribedEvent instanceof BackedEnum) ? (string) $subscribedEvent->value : $subscribedEvent;

            if ($event !== $eventName) {
                continue;
            }

            RecordChanged::make($record)
                ->event($subscribedEvent)
                ->dispatch();

            return;
        }
    }
}
