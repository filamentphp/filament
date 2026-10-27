<?php

namespace Filament\Tests\Upgrade\Fixtures\PersistentUploadRestriction;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;

class Form
{
    use InteractsWithSchemas;
    use RestrictsFileUploadsToSchemaComponents;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PersistentUploadRestriction;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;

class Form
{
    use InteractsWithSchemas;
}
