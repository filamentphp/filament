<?php

namespace Filament\Tests\Upgrade\Fixtures\PageConfiguration;

use Filament\Auth\Pages\EditProfile;
use Filament\Pages\Concerns\HasMaxWidth;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

final class Login extends SimplePage
{
    protected Width | string | null $maxWidth = Width::Large;

    public static bool $formActionsAreSticky = true;

    public function getMaxWidth(): Width | string | null
    {
        return parent::getMaxWidth() ?? $this->maxWidth;
    }

    public function widthFromClassName(): Width | string | null
    {
        $page = self::class;

        return $page::getMaxWidth();
    }

    public function update(): void
    {
        $this->maxWidth = Width::Small;
        static::$formActionsAreSticky = false;
    }
}

final class Profile extends EditProfile
{
    protected Width | string | null $maxWidth = null;
}

final class TraitPage extends SimplePage
{
    use HasMaxWidth;
}

function inspect(?SimplePage $page): void
{
    $page?->getMaxWidth();
    Login::$formActionsAreSticky = true;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PageConfiguration;

use Filament\Auth\Pages\EditProfile;
use Filament\Pages\Concerns\HasMaxWidth;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

final class Login extends SimplePage
{
    protected Width | string | null $maxWidth = Width::Large;

    public static bool $areFormActionsSticky = true;

    public function getMaxWidth(): Width | string | null
    {
        return parent::getMaxWidth() ?? $this->maxWidth;
    }

    public function widthFromClassName(): Width | string | null
    {
        $page = self::class;

        return $page::getMaxWidth();
    }

    public function update(): void
    {
        $this->maxWidth = Width::Small;
        static::$areFormActionsSticky = false;
    }
}

final class Profile extends EditProfile
{
    protected Width | string | null $maxWidth = null;
}

final class TraitPage extends SimplePage
{
    use HasMaxWidth;
}

function inspect(?SimplePage $page): void
{
    $page?->getMaxWidth();
    Login::$areFormActionsSticky = true;
}
