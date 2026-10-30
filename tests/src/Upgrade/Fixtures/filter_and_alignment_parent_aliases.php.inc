<?php

namespace Filament\Tests\Upgrade\Fixtures\FilterAndAlignmentParents;

use Filament\Pages\Page;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CustomFilter extends SelectFilter
{
    public function queriesRelationships(): bool
    {
        return parent::queriesRelationships();
    }

    public function getRelationshipKey(?Builder $query = null): ?string
    {
        return parent::getRelationshipKey(query: $query);
    }
}

class InheritedFilter extends CustomFilter {}

class CustomPage extends Page
{
    public static function alignFormActionsLeft(): void
    {
        parent::alignFormActionsLeft();
    }

    public static function configureRight(): void
    {
        parent::alignFormActionsRight();
    }

    public function configureLeft(): void
    {
        parent::alignFormActionsLeft();
    }
}

class InheritedPage extends CustomPage {}

function configure(CustomFilter $filter): array
{
    CustomPage::alignFormActionsLeft();
    CustomPage::configureRight();

    return [$filter->queriesRelationships(), $filter->getRelationshipKey()];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\FilterAndAlignmentParents;

use Filament\Pages\Page;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CustomFilter extends SelectFilter
{
    public function queriesRelationships(): bool
    {
        return parent::hasRelationship();
    }

    public function getRelationshipKey(?Builder $query = null): ?string
    {
        return parent::getQualifiedRelatedKeyNameForRelationship(query: $query);
    }
}

class InheritedFilter extends CustomFilter {}

class CustomPage extends Page
{
    public static function alignFormActionsLeft(): void
    {
        parent::alignFormActionsStart();
    }

    public static function configureRight(): void
    {
        parent::alignFormActionsEnd();
    }

    public function configureLeft(): void
    {
        parent::alignFormActionsStart();
    }
}

class InheritedPage extends CustomPage {}

function configure(CustomFilter $filter): array
{
    CustomPage::alignFormActionsLeft();
    CustomPage::configureRight();

    return [$filter->queriesRelationships(), $filter->getRelationshipKey()];
}
