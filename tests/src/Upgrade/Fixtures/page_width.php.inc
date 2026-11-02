<?php

namespace Filament\Tests\Upgrade\Fixtures\PageWidth;

use Filament\Auth\Pages\EditProfile;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

trait WidthConfiguration
{
    protected Width | string | null $maxWidth = null;

    public function getMaxWidth(): Width | string | null
    {
        return parent::getMaxWidth() ?? 'fallback-width';
    }

    public function configureWidth(Width | string | null $width): void
    {
        $this->maxWidth = $width;
    }
}

class Login extends SimplePage
{
    use WidthConfiguration {
        getMaxWidth as originalWidth;
    }
}

class ChildLogin extends Login {}

class Profile extends EditProfile
{
    protected Width | string | null $maxWidth = Width::ExtraLarge;
}

class GetterOnly extends SimplePage
{
    public function getMaxWidth(): Width | string | null
    {
        return parent::getMaxWidth() ?? Width::Medium;
    }
}

class Unrelated
{
    public string $maxWidth = 'unrelated';

    public function getMaxWidth(): string
    {
        return $this->maxWidth;
    }
}

function width(?SimplePage $page): Width | string | null
{
    return $page?->getMaxWidth();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PageWidth;

use Filament\Auth\Pages\EditProfile;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

trait WidthConfiguration
{
    protected Width | string | null $maxContentWidth = null;

    public function getMaxContentWidth(): Width | string | null
    {
        return parent::getMaxContentWidth() ?? 'fallback-width';
    }

    public function configureWidth(Width | string | null $width): void
    {
        $this->maxContentWidth = $width;
    }
}

class Login extends SimplePage
{
    use WidthConfiguration {
        getMaxContentWidth as originalWidth;
    }
}

class ChildLogin extends Login {}

class Profile extends EditProfile
{
    protected Width | string | null $maxContentWidth = Width::ExtraLarge;
}

class GetterOnly extends SimplePage
{
    public function getMaxContentWidth(): Width | string | null
    {
        return parent::getMaxContentWidth() ?? Width::Medium;
    }
}

class Unrelated
{
    public string $maxWidth = 'unrelated';

    public function getMaxWidth(): string
    {
        return $this->maxWidth;
    }
}

function width(?SimplePage $page): Width | string | null
{
    return $page?->getMaxContentWidth();
}
