<?php

namespace Filament\Billing\Providers;

use Closure;
use Exception;
use Filament\Billing\Providers\Http\Middleware\VerifySparkBillableIsSubscribed;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Spark\Spark;

class SparkBillingProvider implements Contracts\Provider
{
    /**
     * @return string | Closure | array<class-string, string>
     */
    public function getRouteAction(): string | Closure | array
    {
        return function (): RedirectResponse {
            // We cannot explicitly require Spark through Composer, as Stripe
            // and Paddle integrations have different packages associated
            // with them. As such, we must check if this class exists.
            if (! class_exists(Spark::class)) {
                throw new Exception('Laravel Spark is not installed.');
            }

            $tenant = Filament::getTenant();

            return redirect()->route('spark.portal', [
                'id' => $tenant->getKey(),
                'type' => (fn (): string => $this->type)->call(Spark::billable($tenant::class)),
            ]);
        };
    }

    public function getSubscribedMiddleware(): string
    {
        return VerifySparkBillableIsSubscribed::class;
    }
}
