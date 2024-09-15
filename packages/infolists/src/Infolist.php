<?php

namespace Filament\Infolists;

class Infolist extends ComponentContainer
{
    protected string $name;

    public static string $defaultCurrency = 'usd';

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultDateIsoDisplayFormat = 'L';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    public static string $defaultDateTimeIsoDisplayFormat = 'LLL';

    public static string $defaultTimeDisplayFormat = 'H:i:s';

    public static string $defaultTimeIsoDisplayFormat = 'LT';

    public static ?string $defaultNumberLocale = null;

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
