<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Support\Concerns\HasLoadingPlaceholder as HasBaseLoadingPlaceholder;
use Illuminate\Contracts\View\View;

trait HasLoadingPlaceholder
{
    use HasBaseLoadingPlaceholder;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function wrapAsyncComponentHtml(string $html, string $key): string
    {
        $loadingPlaceholder = $this->getLoadingPlaceholder() ?? view('filament::components.loading-placeholder');

        ob_start(); ?>

        <div
            x-data="{ isLoading: true }"
            x-on:async-component-ready.stop="isLoading = false"
            x-bind:data-loading="isLoading"
            data-loading="true"
            wire:key="<?= e($key) ?>.loading"
            class="fi-async-component"
        >
            <div
                x-show="isLoading"
                role="status"
                aria-live="polite"
                aria-busy="true"
                x-bind:aria-busy="isLoading ? 'true' : 'false'"
                class="fi-async-component-loading"
            >
                <span class="fi-sr-only"><?= e(__('filament::loading.label')) ?></span>

                <div aria-hidden="true" class="fi-loading-placeholder-content">
                    <?= ($loadingPlaceholder instanceof View) ? $loadingPlaceholder->render() : e($loadingPlaceholder) ?>
                </div>
            </div>

            <?= $html ?>
        </div>

        <?php return ob_get_clean();
    }
}
