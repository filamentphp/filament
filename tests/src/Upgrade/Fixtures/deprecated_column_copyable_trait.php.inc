<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedColumnCopyableTrait;

use Filament\Tables\Columns\Concerns\CanBeCopied as LegacyCopy;

trait CustomCopy
{
    public function isCopyable(): bool
    {
        return false;
    }
}

class ImportedCopy
{
    use LegacyCopy, CustomCopy {
        LegacyCopy::isCopyable insteadof CustomCopy;
        LegacyCopy::copyable as configureCopy;
    }
}

class QualifiedCopy
{
    use \Filament\Tables\Columns\Concerns\CanBeCopied;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedColumnCopyableTrait;

use Filament\Support\Concerns\CanBeCopied;

trait CustomCopy
{
    public function isCopyable(): bool
    {
        return false;
    }
}

class ImportedCopy
{
    use CanBeCopied, CustomCopy {
        CanBeCopied::isCopyable insteadof CustomCopy;
        CanBeCopied::copyable as configureCopy;
    }
}

class QualifiedCopy
{
    use CanBeCopied;
}
