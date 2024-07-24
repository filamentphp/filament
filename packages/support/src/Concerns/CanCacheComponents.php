<?php

    namespace Filament\Support\Concerns;

    use Filament\PanelProvider;

    trait CanCacheComponents
    {
        protected function canCacheComponents(): bool
        {
            return class_exists(PanelProvider::class);
        }
    }
