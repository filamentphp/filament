<?php

namespace Filament\Upgrade\Support;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\PhpParser\AstResolver;

class ParameterRenameGuard
{
    public function __construct(
        private TraitConsumerResolver $traitConsumerResolver,
        private AstResolver $astResolver,
        private NodeFinder $nodeFinder,
    ) {}

    /**
     * @param  class-string  $owner
     */
    public function canRename(string $owner, string $method, string $oldName, string $newName, string $currentFilePath): bool
    {
        foreach ($this->traitConsumerResolver->getSourceClasses($currentFilePath) as $class) {
            if ((! $class->hasNativeMethod($method)) || (! $this->supports($class, $owner, $currentFilePath))) {
                continue;
            }

            $reflection = $class->getNativeMethod($method);
            $parameters = array_map(static fn ($parameter): string => $parameter->getName(), $reflection->getVariants()[0]->getParameters());

            if (! in_array($oldName, $parameters, true)) {
                continue;
            }

            if (in_array($newName, $parameters, true)) {
                return false;
            }

            $trait = method_exists($reflection, 'getDeclaringTrait') ? $reflection->getDeclaringTrait() : null;
            $declaration = $this->astResolver->resolveClassMethod(($trait ?? $reflection->getDeclaringClass())->getName(), $method);

            // A reference alias must not merge the parameter with an independent
            // destination local. Keep declarations and all possible callers together.
            if ($declaration && $this->nodeFinder->findFirst($declaration->stmts ?? [], static fn (Node $node): bool => ($node instanceof Variable) && ($node->name === $newName))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  class-string  $owner
     */
    private function supports(ClassReflection $class, string $owner, string $currentFilePath): bool
    {
        $ownerType = new ObjectType($owner);

        if ($ownerType->isSuperTypeOf(new ObjectType($class->getName()))->yes() || $class->hasTraitUse($owner)) {
            return true;
        }

        $consumers = $class->isTrait() ? $this->traitConsumerResolver->getConsumers($class, $currentFilePath) : [];

        if (! $consumers) {
            return false;
        }

        foreach ($consumers as $consumer) {
            if ((! $ownerType->isSuperTypeOf(new ObjectType($consumer->getName()))->yes()) && (! $consumer->hasTraitUse($owner))) {
                return false;
            }
        }

        return true;
    }
}
