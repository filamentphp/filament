<?php

namespace Filament\Support\Enums;

enum Platform
{
    case Windows;

    case Linux;

    case Mac;

    case Other;

    public static function detect(): Platform
    {
        $userAgent = request()->userAgent();

        return match (true) {
            str_contains($userAgent, 'Windows') => self::Windows,
            str_contains($userAgent, 'Mac') => self::Mac,
            str_contains($userAgent, 'Linux') => self::Linux,
            default => self::Other,
        };
    }
}
