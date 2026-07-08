<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Tags\Tag;

trait InteractsWithSpatieTags
{
    protected string | Closure | AllTagTypes | null $type;

    public function type(string | Closure | AllTagTypes | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string | AllTagTypes | null
    {
        return $this->evaluate($this->type);
    }

    public function isAnyTagTypeAllowed(): bool
    {
        return $this->getType() instanceof AllTagTypes;
    }

    /**
     * @return class-string<Tag>
     */
    public function getTagClassName(): string
    {
        $model = $this->getModel();

        return ($model && method_exists($model, 'getTagClassName'))
            ? $model::getTagClassName()
            : config('tags.tag_model', Tag::class);
    }

    /**
     * @return array<string>
     */
    public function getTagSuggestions(): array
    {
        $type = $this->getType();
        $query = $this->getTagClassName()::query();

        if (! $this->isAnyTagTypeAllowed()) {
            $query->when(
                filled($type),
                fn (Builder $query) => $query->where('type', $type),
                fn (Builder $query) => $query->where('type', null),
            );
        }

        return $query->pluck('name')->all();
    }

    /**
     * Resolves the tag names to tag models, creating any tags that do not already exist.
     *
     * When a specific type is set, tags are found or created with that type. When any tag
     * type is allowed, existing tags of any type that match each name are used, and a new
     * untyped tag is created if no match exists, mirroring the behavior of `SpatieTagsInput`.
     *
     * @param  array<string>  $tagNames
     * @return Collection<int, Tag>
     */
    public function resolveTagsForAttaching(array $tagNames): Collection
    {
        $tagClassName = $this->getTagClassName();

        if (! $this->isAnyTagTypeAllowed()) {
            return collect($tagClassName::findOrCreate($tagNames, $this->getType()));
        }

        return collect($tagNames)
            ->map(static function (string $tagName) use ($tagClassName) {
                $locale = $tagClassName::getLocale();

                $tags = $tagClassName::findFromStringOfAnyType($tagName, $locale);

                if ($tags?->isEmpty() ?? true) {
                    return $tagClassName::create([
                        'name' => [$locale => $tagName],
                    ]);
                }

                return $tags;
            })
            ->flatten();
    }

    /**
     * Resolves the tag names to existing tag models. Tags that do not exist are ignored,
     * since there is nothing to detach.
     *
     * When a specific type is set, only tags of that type are matched. When any tag type
     * is allowed, all tags of any type that match each name are detached.
     *
     * @param  array<string>  $tagNames
     * @return Collection<int, Tag>
     */
    public function resolveTagsForDetaching(array $tagNames): Collection
    {
        $tagClassName = $this->getTagClassName();

        if (! $this->isAnyTagTypeAllowed()) {
            $type = $this->getType();

            return collect($tagNames)
                ->map(static fn (string $tagName) => $tagClassName::findFromString($tagName, $type))
                ->filter();
        }

        return collect($tagNames)
            ->map(static fn (string $tagName) => $tagClassName::findFromStringOfAnyType($tagName))
            ->flatten()
            ->filter();
    }
}
