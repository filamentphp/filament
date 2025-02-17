<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasRelationLink
{
    protected string | Closure | null $relationResourceClass = null;

    protected string | Closure | null $relationForeignKey = null;
    protected string | Closure | null $relationLinkColor = 'primary';

    public function relationLink(string | Closure $resourceClass, string | Closure $foreignKey, string | Closure $color = 'primary'): static
    {
        $this->relationResourceClass = $resourceClass;
        $this->relationForeignKey = $foreignKey;
        $this->relationLinkColor = $color;

        $this->url(function (Model $record) {
            $resource = $this->evaluate($this->relationResourceClass);
            $foreignKey = $this->evaluate($this->relationForeignKey);

            return $resource::getUrl('view', ['record' => $record->{$foreignKey}]);
        });

        $this->color($this->relationLinkColor);

        return $this;
    }
}
