<?php

    namespace Filament\Support\Commands\Concerns;

    use Filament\PanelProvider;

    trait CanCacheComponents
    {
        protected function canCacheComponents(): bool
        {
            return class_exists(PanelProvider::class);
        }
    }
