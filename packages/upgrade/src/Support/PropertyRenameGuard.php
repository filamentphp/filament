<?php

namespace Filament\Upgrade\Support;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;

class PropertyRenameGuard
{
    public function __construct(
        private TraitConsumerResolver $traitConsumerResolver,
        private ParameterRenameGuard $parameterRenameGuard,
    ) {}

    /**
     * @param  class-string  $owner
     */
    public function canRename(ClassReflection $class, string $owner, string $oldName, string $newName, string $currentFilePath): bool
    {
        $ownerType = new ObjectType($owner);
        $consumers = $class->isTrait()
            ? $this->traitConsumerResolver->getConsumers($class, $currentFilePath)
            : [$class];

        if (! $consumers) {
            return false;
        }

        foreach ($consumers as $consumer) {
            if (! $ownerType->isSuperTypeOf(new ObjectType($consumer->getName()))->yes()) {
                return false;
            }

            foreach ([$consumer, ...$consumer->getParents()] as $ancestor) {
                foreach ($ancestor->getTraits(true) as $trait) {
                    if (! $trait->hasNativeProperty($oldName)) {
                        continue;
                    }

                    $traitConsumers = $this->traitConsumerResolver->getConsumers($trait, $currentFilePath);

                    if (! $traitConsumers) {
                        return false;
                    }

                    foreach ($traitConsumers as $traitConsumer) {
                        if (! $ownerType->isSuperTypeOf(new ObjectType($traitConsumer->getName()))->yes()) {
                            return false;
                        }
                    }
                }
            }
        }

        $classes = $this->traitConsumerResolver->getSourceClasses($currentFilePath);

        foreach ($consumers as $consumer) {
            $classes[$consumer->getName()] = $consumer;
        }

        foreach ($classes as $candidate) {
            if ((! $ownerType->isSuperTypeOf(new ObjectType($candidate->getName()))->yes())
                || (! $candidate->hasNativeProperty($oldName))) {
                continue;
            }

            // A promoted property shares its constructor parameter's name. A
            // collision in any indexed constructor, even a non-promoted sibling,
            // keeps that parameter family and its property accesses unchanged.
            if ($candidate->getNativeProperty($oldName)->isPromoted()
                && (! $this->parameterRenameGuard->canRename($owner, '__construct', $oldName, $newName, $currentFilePath))) {
                return false;
            }

            // The framework's canonical backing property is expected. An application
            // property with the new name is separate state, including inherited and
            // promoted declarations. Keep the whole mapped family unchanged: callers
            // typed as a parent can receive either a colliding or non-colliding child.
            if ($candidate->hasNativeProperty($newName) && ($candidate->getNativeProperty($newName)->getDeclaringClass()->getName() !== $owner)) {
                return false;
            }
        }

        return true;
    }
}
