<?php

namespace Filament\Resources\Concerns;

interface IsGroupResource
{
    public static function registerNavigationGroup(): void;

    /**
     * This method helps define what menus a group should be included in.
     * @return array<string>
     */
    public static function getGroupMenusList(): array;

    public static function generateUrl();
    public static function defaultActiveRule();
}
