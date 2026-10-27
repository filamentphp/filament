<?php

namespace Filament\Tests\Upgrade\Fixtures\PersistentIndirectUnions;

use Closure;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;

class BaseColumn extends ImageColumn {}

class CustomColumn extends BaseColumn
{
    public function getHeight(): string
    {
        return 'custom legacy getter';
    }
}

function inspectColumn(BaseColumn | CustomColumn $column): string
{
    return $column->getHeight();
}

class BaseSchema extends Schema {}

class CustomSchema extends BaseSchema
{
    public function isLoadingDeferred(): bool
    {
        return false;
    }

    public function isLazyLoaded(): bool
    {
        return true;
    }
}

function inspectSchema(BaseSchema | CustomSchema $schema): bool
{
    return $schema->isLoadingDeferred();
}

class ReviewableSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = true;

    public function isLoadingDeferred(): bool
    {
        return parent::isLoadingDeferred();
    }

    public function inspectProperty(): bool
    {
        return $this->evaluate($this->isLoadingDeferred);
    }

    public function configure(): void
    {
        $this->deferLoading(condition: false);
    }
}
