<?php

namespace Filament\Schemas\Concerns;

/**
 * @deprecated Restricting file uploads to schema components is enabled by default
 *             for all components using the `InteractsWithSchemas` trait. This trait
 *             is not required and is kept only for backwards compatibility. To opt
 *             out, override `shouldRestrictFileUploadsToSchemaComponents()` to
 *             return `false`.
 */
trait RestrictsFileUploadsToSchemaComponents /** @phpstan-ignore trait.unused */
{
    //
}
