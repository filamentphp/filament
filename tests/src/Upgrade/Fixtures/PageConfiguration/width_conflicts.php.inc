<?php

namespace Filament\Tests\Upgrade\Fixtures\WidthConflicts;

use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

class ParentLogin extends SimplePage
{
    protected Width | string | null $maxWidth = Width::Small;
}

class Login extends ParentLogin
{
    protected Width | string | null $maxWidth = Width::Small;

    protected Width | string | null $maxContentWidth = Width::Large;

    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}

class OtherLogin extends ParentLogin {}

function otherWidth(OtherLogin $page): Width | string | null
{
    return $page->getMaxWidth();
}

class IndependentLogin extends SimplePage
{
    protected Width | string | null $maxWidth = Width::ExtraLarge;
}

class ExternalReaderLogin extends \Filament\Tests\Upgrade\Fixtures\PageConfiguration\ExcludedWidthReader
{
    protected Width | string | null $maxWidth = Width::Small;
}

class ExternalChildLogin extends \Filament\Tests\Upgrade\Fixtures\PageConfiguration\ExcludedLogin
{
    public function configuredWidth(): Width | string | null
    {
        return $this->getMaxWidth();
    }
}

function independentWidth(IndependentLogin $page): Width | string | null
{
    return $page->getMaxWidth();
}

trait ReadsWidth
{
    public function configuredWidth(): Width | string | null
    {
        return $this->getMaxWidth();
    }
}

class ReaderLogin extends SimplePage
{
    use ReadsWidth;

    protected Width | string | null $maxWidth = Width::Small;
}

class ConflictingReaderLogin extends SimplePage
{
    use ReadsWidth;

    protected Width | string | null $maxWidth = Width::Medium;

    protected Width | string | null $maxContentWidth = Width::Large;
}

trait WidthStorage
{
    protected Width | string | null $maxWidth = null;

    public function configureWidth(Width $width): void
    {
        $this->maxWidth = $width;
    }
}

trait WidthGetter
{
    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}

class StorageLogin extends SimplePage
{
    use WidthStorage;

    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}

class BridgeLogin extends SimplePage
{
    use WidthStorage, WidthGetter;
}

class ConflictingTraitLogin extends SimplePage
{
    use WidthGetter;

    protected Width | string | null $maxContentWidth = Width::Large;
}

function width(SimplePage $page): Width | string | null
{
    return $page->getMaxWidth();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\WidthConflicts;

use Filament\Tests\Upgrade\Fixtures\PageConfiguration\ExcludedWidthReader;
use Filament\Tests\Upgrade\Fixtures\PageConfiguration\ExcludedLogin;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;

class ParentLogin extends SimplePage
{
    protected Width | string | null $maxWidth = Width::Small;
}

class Login extends ParentLogin
{
    protected Width | string | null $maxWidth = Width::Small;

    protected Width | string | null $maxContentWidth = Width::Large;

    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}

class OtherLogin extends ParentLogin {}

function otherWidth(OtherLogin $page): Width | string | null
{
    return $page->getMaxWidth();
}

class IndependentLogin extends SimplePage
{
    protected Width | string | null $maxContentWidth = Width::ExtraLarge;
}

class ExternalReaderLogin extends ExcludedWidthReader
{
    protected Width | string | null $maxWidth = Width::Small;
}

class ExternalChildLogin extends ExcludedLogin
{
    public function configuredWidth(): Width | string | null
    {
        return $this->getMaxWidth();
    }
}

function independentWidth(IndependentLogin $page): Width | string | null
{
    return $page->getMaxContentWidth();
}

trait ReadsWidth
{
    public function configuredWidth(): Width | string | null
    {
        return $this->getMaxWidth();
    }
}

class ReaderLogin extends SimplePage
{
    use ReadsWidth;

    protected Width | string | null $maxWidth = Width::Small;
}

class ConflictingReaderLogin extends SimplePage
{
    use ReadsWidth;

    protected Width | string | null $maxWidth = Width::Medium;

    protected Width | string | null $maxContentWidth = Width::Large;
}

trait WidthStorage
{
    protected Width | string | null $maxWidth = null;

    public function configureWidth(Width $width): void
    {
        $this->maxWidth = $width;
    }
}

trait WidthGetter
{
    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}

class StorageLogin extends SimplePage
{
    use WidthStorage;

    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}

class BridgeLogin extends SimplePage
{
    use WidthStorage, WidthGetter;
}

class ConflictingTraitLogin extends SimplePage
{
    use WidthGetter;

    protected Width | string | null $maxContentWidth = Width::Large;
}

function width(SimplePage $page): Width | string | null
{
    return $page->getMaxWidth();
}
