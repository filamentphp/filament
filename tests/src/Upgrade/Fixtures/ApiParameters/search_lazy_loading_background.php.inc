<?php

namespace Filament\Tests\Upgrade\Fixtures\SearchLazyLoadingBackground;

use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\Providers\DefaultGlobalSearchProvider;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;

trait SearchResults
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $capture = static fn (): string => $query;

        return GlobalSearchResults::make()->category($capture());
    }
}

class Provider implements GlobalSearchProvider
{
    use SearchResults;
}

class InheritedProvider extends DefaultGlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        return parent::getResults(query: $query);
    }
}

class ColorMap extends ButtonComponentColorMap
{
    protected function probeTextLightness(array $textForBg): array
    {
        return parent::probeTextLightness(textForBg: $textForBg);
    }
}

class Unrelated
{
    public function getResults(string $query): string
    {
        return $query;
    }

    public function databaseNotifications(bool $isLazy): bool
    {
        return $isLazy;
    }

    public function lightBackground(int $bg): int
    {
        return $bg;
    }
}

function configure(Panel $panel, GlobalSearchProvider $provider, ?DefaultGlobalSearchProvider $optionalProvider, Unrelated $unrelated): array
{
    $panel->databaseNotifications(isLazy: false);
    $panel->databaseNotifications(...['isLazy' => static fn (): bool => true]);
    $provider->getResults(query: 'invoice');
    $optionalProvider?->getResults(query: 'receipt');
    $provider->getResults(...['query' => 'customer']);

    return [
        ColorMap::make(Color::Red)
            ->lightBackground(bg: 800, hover: 700)
            ->darkBackground(...['bg' => 600, 'hover' => 500])
            ->get(),
        $unrelated->getResults(query: 'unchanged'),
        $unrelated->databaseNotifications(isLazy: false),
        $unrelated->lightBackground(bg: 300),
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SearchLazyLoadingBackground;

use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\Providers\DefaultGlobalSearchProvider;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;

trait SearchResults
{
    public function getResults(string $search): ?GlobalSearchResults
    {
        $query =& $search;
        $capture = static fn (): string => $query;

        return GlobalSearchResults::make()->category($capture());
    }
}

class Provider implements GlobalSearchProvider
{
    use SearchResults;
}

class InheritedProvider extends DefaultGlobalSearchProvider
{
    public function getResults(string $search): ?GlobalSearchResults
    {
        $query =& $search;
        return parent::getResults(search: $query);
    }
}

class ColorMap extends ButtonComponentColorMap
{
    protected function probeTextLightness(array $textForBackground): array
    {
        $textForBg =& $textForBackground;
        return parent::probeTextLightness(textForBackground: $textForBg);
    }
}

class Unrelated
{
    public function getResults(string $query): string
    {
        return $query;
    }

    public function databaseNotifications(bool $isLazy): bool
    {
        return $isLazy;
    }

    public function lightBackground(int $bg): int
    {
        return $bg;
    }
}

function configure(Panel $panel, GlobalSearchProvider $provider, ?DefaultGlobalSearchProvider $optionalProvider, Unrelated $unrelated): array
{
    $panel->databaseNotifications(isLazyLoaded: false);
    $panel->databaseNotifications(...['isLazyLoaded' => static fn (): bool => true]);
    $provider->getResults(search: 'invoice');
    $optionalProvider?->getResults(search: 'receipt');
    $provider->getResults(...['search' => 'customer']);

    return [
        ColorMap::make(Color::Red)
            ->lightBackground(background: 800, hover: 700)
            ->darkBackground(...['background' => 600, 'hover' => 500])
            ->get(),
        $unrelated->getResults(query: 'unchanged'),
        $unrelated->databaseNotifications(isLazy: false),
        $unrelated->lightBackground(bg: 300),
    ];
}
