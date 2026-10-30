<?php

namespace Filament\Upgrade\Rector;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenamePlainSchemaAliasesRector extends AbstractRector
{
    /** @var array<string, array{string, list<string>}> */
    private const METHODS = [
        'cacheform' => ['cacheSchema', ['name', 'form']],
        'cacheinfolist' => ['cacheSchema', ['name', 'schema']],
        'hascachedform' => ['hasCachedSchema', ['name']],
        'hascachedinfolist' => ['hasCachedSchema', ['name']],
        'getform' => ['getSchema', ['name']],
        'getinfolist' => ['getSchema', ['name']],
        'getcachedforms' => ['getCachedSchemas', []],
        'getcachedinfolists' => ['getCachedSchemas', []],
        'iscachingforms' => ['isCachingSchemas', []],
        'getactiveformslocale' => ['getActiveSchemaLocale', []],
        'getoldformstate' => ['getOldSchemaState', ['statePath']],
    ];

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /** @param MethodCall | NullsafeMethodCall | StaticCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        $alias = strtolower($node->name->toString());

        if (! isset(self::METHODS[$alias])) {
            return null;
        }

        [$replacement, $parameters] = self::METHODS[$alias];

        // Keep required cache arguments explicit: `cacheSchema($name)` builds
        // a schema, while `cacheSchema($name, null)` removes it.
        if (count($node->args) !== count($parameters)) {
            return null;
        }

        foreach ($node->args as $argument) {
            if ($argument->unpack || ($argument->name && ! in_array($argument->name->toString(), $parameters, true))) {
                return null;
            }
        }

        if ($node instanceof StaticCall) {
            if ((! $node->class instanceof Name) || (! $this->isNames($node->class, ['parent', 'self', 'static']))) {
                return null;
            }

            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $class = $this->isName($node->class, 'parent') ? $context?->getParentClass() : $context;

            if ((! $class) || (! $this->owns($class, $alias))) {
                return null;
            }
        } else {
            $classes = TypeCombinator::removeNull($this->receiverType($node->var))->getObjectClassReflections();

            if ((! count($classes)) || array_filter($classes, fn (ClassReflection $class): bool => ! $this->owns($class, $alias))) {
                return null;
            }
        }

        foreach ($node->args as $argument) {
            if (($alias === 'cacheform') && $argument->name?->toString() === 'form') {
                $argument->name = new Identifier('schema');
            }
        }

        if ($node instanceof StaticCall) {
            return new MethodCall(new Variable('this'), $replacement, $node->args);
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    private function owns(ClassReflection $class, string $alias): bool
    {
        // Application-defined methods with the old name need manual migration.
        if ($class->hasNativeMethod($alias)) {
            return false;
        }

        $owner = str_contains($alias, 'infolist') ? InteractsWithInfolists::class : InteractsWithForms::class;

        foreach ($class->getAncestors() as $ancestor) {
            if (($ancestor->getName() === $owner) || $ancestor->hasTraitUse($owner)) {
                return true;
            }
        }

        return false;
    }

    private function receiverType(Expr $receiver): Type
    {
        $type = $this->getType($receiver);

        if ((! ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall)) || (! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
            return $type;
        }

        $owner = $this->receiverType($receiver->var);

        foreach ($owner->getObjectClassReflections() as $class) {
            if ((! $class->hasNativeMethod($receiver->name->toString())) || (! $class->getNativeMethod($receiver->name->toString())->getVariants()[0]->getReturnType() instanceof StaticType)) {
                return $type;
            }
        }

        return count($owner->getObjectClassReflections()) ? $owner : $type;
    }
}
