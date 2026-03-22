<?php

namespace Filament\Support\EloquentSerializer;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;
use RuntimeException;

class EloquentSerializer
{
    use Grammars\EloquentBuilderGrammar;
    use Grammars\ModelGrammar;
    use Grammars\QueryBuilderGrammar;

    /**
     * @throws RuntimeException
     */
    public function serialize(Builder | Relation $builder): string
    {
        if (
            $builder instanceof HasOne
            || $builder instanceof HasMany
            || $builder instanceof BelongsTo // as well as `MorphTo`
            || $builder instanceof MorphOne
        ) {
            $builder = $builder->getQuery(); // chaperone/inverse is not supported!
        }

        if ($builder instanceof Relation) {
            throw new RuntimeException(get_class($builder) . ' cannot be packed.');
        }

        $package = $this->pack($builder);

        return serialize($package); // important!
    }

    /**
     * @throws LogicException
     */
    public function unserialize(Package | string $package): Builder
    {
        // Prepare data
        if (is_string($package)) {
            $oldClass = 'AnourValar\EloquentSerialize\Package';
            $newClass = Package::class;

            // Replace the class reference in the serialized object header (`O:36:"..."`)
            $package = str_replace(
                'O:' . strlen($oldClass) . ':"' . $oldClass . '"',
                'O:' . strlen($newClass) . ':"' . $newClass . '"',
                $package,
            );

            // Replace the class prefix in serialized private properties (`s:42:"\0...\0data"`)
            $package = str_replace(
                's:' . (strlen($oldClass) + 6) . ':"' . "\0" . $oldClass . "\0" . 'data"',
                's:' . (strlen($newClass) + 6) . ':"' . "\0" . $newClass . "\0" . 'data"',
                $package,
            );

            $package = unserialize($package);
        }
        if (! ($package instanceof Package)) {
            throw new LogicException('Incorrect argument.');
        }

        // Unpack
        return $this->unpack($package);
    }
}
