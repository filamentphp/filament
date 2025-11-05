<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Table\Tree;

trait HasTree
{
    protected ?Tree $tree = null;

    public function tree(?Closure $callback = null): Tree
    {
        $this->tree ??= Tree::make()->table($this);

        if ($callback) {
            $this->evaluate($callback, [
                'tree' => $this->tree,
            ]);
        }

        return $this->tree;
    }

    public function hasTree(): bool
    {
        return $this->tree?->isEnabled() ?? false;
    }

    public function getTree(): ?Tree
    {
        return $this->tree;
    }
}
