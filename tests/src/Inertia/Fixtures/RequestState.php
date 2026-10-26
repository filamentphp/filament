<?php

namespace Filament\Tests\Inertia\Fixtures;

class RequestState
{
    public static bool $allowed = true;

    public static int $responses = 0;

    public static int $mounts = 0;

    public static int $terminations = 0;

    public static int $shares = 0;
}
