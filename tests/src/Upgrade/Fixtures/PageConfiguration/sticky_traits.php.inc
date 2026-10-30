<?php

namespace Filament\Tests\Upgrade\Fixtures\StickyTraits;

use Filament\Pages\SimplePage;

trait StickyConfiguration
{
    public static bool $formActionsAreSticky = false;

    public static function disable(): void
    {
        self::$formActionsAreSticky = false;
    }

    public function enable(): void
    {
        $this::$formActionsAreSticky = true;
    }
}

trait NestedConfiguration
{
    use StickyConfiguration;

    public function configuredSticky(): bool
    {
        return static::$formActionsAreSticky;
    }
}

class ParentPage extends SimplePage
{
    use NestedConfiguration;
}

class ChildPage extends ParentPage
{
    public function configure(bool $condition): void
    {
        parent::$formActionsAreSticky = $condition;
    }
}

function configure(ChildPage $page): void
{
    ChildPage::$formActionsAreSticky = false;
    $page::$formActionsAreSticky = true;
}

/** @param class-string<ChildPage> $page */
function configureClass(string $page): void
{
    $page::$formActionsAreSticky = true;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\StickyTraits;

use Filament\Pages\SimplePage;

trait StickyConfiguration
{
    public static bool $areFormActionsSticky = false;

    public static function disable(): void
    {
        self::$areFormActionsSticky = false;
    }

    public function enable(): void
    {
        $this::$areFormActionsSticky = true;
    }
}

trait NestedConfiguration
{
    use StickyConfiguration;

    public function configuredSticky(): bool
    {
        return static::$areFormActionsSticky;
    }
}

class ParentPage extends SimplePage
{
    use NestedConfiguration;
}

class ChildPage extends ParentPage
{
    public function configure(bool $condition): void
    {
        parent::$areFormActionsSticky = $condition;
    }
}

function configure(ChildPage $page): void
{
    ChildPage::$areFormActionsSticky = false;
    $page::$areFormActionsSticky = true;
}

/** @param class-string<ChildPage> $page */
function configureClass(string $page): void
{
    $page::$areFormActionsSticky = true;
}
