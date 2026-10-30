<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeTraverser;
use Rector\Configuration\RenamedClassesDataCollector;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenameSchemaComponentHooksRector extends AbstractRector
{
    public function __construct(
        private AstResolver $astResolver,
        private RenamedClassesDataCollector $renamedClassesDataCollector,
    ) {}

    public function getNodeTypes(): array
    {
        return [ClassMethod::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param  ClassMethod | MethodCall | NullsafeMethodCall | StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $oldName = strtolower($node->name->toString());

        if (in_array($oldName, ['getcomponent', 'getcomponentbystatepath', 'getschemacomponent'], true)) {
            return $node instanceof ClassMethod ? null : $this->renameLookupArgument($node, $oldName);
        }

        $newName = match ($oldName) {
            'getoptionsformcomponents' => 'getOptionsSchemaComponents',
            'getformschema' => 'getSchemaComponents',
            default => null,
        };

        if (! $newName) {
            return null;
        }

        if ($node instanceof ClassMethod) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();

            if ((! $context) || $context->isTrait() || (! $this->supports(new ObjectType($context->getName()), $oldName))) {
                return null;
            }

            // Do not create a duplicate declaration when both hooks exist.
            if ($context->hasNativeMethod($newName) && ($context->getNativeMethod($newName)->getDeclaringClass()->getName() === $context->getName())) {
                return null;
            }
        } elseif (! $this->supports($this->getReceiverType($node), $oldName)) {
            return null;
        }

        $node->name = new Identifier($newName);

        return $node;
    }

    private function getReceiverType(MethodCall | NullsafeMethodCall | StaticCall $node): Type
    {
        if (($node instanceof StaticCall) && $node->class instanceof Name && in_array(strtolower($node->class->toString()), ['parent', 'self', 'static'], true)) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $receiver = ($context && (strtolower($node->class->toString()) === 'parent')) ? $this->getParentClass($context) : $context;

            if ($receiver) {
                return new ObjectType($receiver->getName());
            }
        }

        $type = TypeCombinator::removeNull($this->getType($node instanceof StaticCall ? $node->class : $node->var));

        if (($node instanceof StaticCall) && $type->isClassString()->yes()) {
            $type = $type->getClassStringObjectType();
        }

        return TypeTraverser::map($type, function (Type $type, callable $traverse): Type {
            return $type instanceof ObjectType ? ($this->renamedClassesDataCollector->matchClassName($type) ?? $type) : $traverse($type);
        });
    }

    private function supports(Type $type, string $methodName): bool
    {
        $owners = $methodName === 'getformschema' ? [Operator::class] : [Importer::class, Exporter::class];
        $ownerType = TypeCombinator::union(...array_map(static fn (string $owner): ObjectType => new ObjectType($owner), $owners));

        if ($ownerType->isSuperTypeOf($type)->yes()) {
            return true;
        }

        // Resolve application subclasses of query-builder classes renamed by this set.
        $classes = $type->getObjectClassReflections();

        if (count($classes) !== 1) {
            return false;
        }

        for ($ancestor = $classes[0]; $ancestor; $ancestor = $this->getParentClass($ancestor)) {
            if ($ownerType->isSuperTypeOf(new ObjectType($ancestor->getName()))->yes()) {
                return true;
            }
        }

        return false;
    }

    private function getParentClass(ClassReflection $classReflection): ?ClassReflection
    {
        if ($parent = $classReflection->getParentClass()) {
            return $parent;
        }

        $declaration = $this->astResolver->resolveClassFromClassReflection($classReflection);

        if ((! $declaration instanceof Class_) || (! $declaration->extends)) {
            return null;
        }

        $parentNames = $this->getType($declaration->extends)->getObjectClassNames();

        if (count($parentNames) !== 1) {
            return null;
        }

        $parentType = new ObjectType($parentNames[0]);
        $parentType = $this->renamedClassesDataCollector->matchClassName($parentType) ?? $parentType;

        return $parentType->getObjectClassReflections()[0] ?? null;
    }

    private function renameLookupArgument(MethodCall | NullsafeMethodCall | StaticCall $node, string $methodName): ?Node
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        $type = $this->getReceiverType($node);
        $owner = $methodName === 'getschemacomponent' ? HasSchemas::class : Schema::class;

        if (! (new ObjectType($owner))->isSuperTypeOf($type)->yes()) {
            return null;
        }

        foreach ($type->getObjectClassReflections() as $classReflection) {
            if (! $classReflection->hasNativeMethod($methodName)) {
                continue;
            }

            $method = $classReflection->getNativeMethod($methodName);

            if (! in_array($method->getDeclaringClass()->getName(), [Schema::class, HasSchemas::class], true)) {
                if ((! method_exists($method, 'getDeclaringTrait')) || ($method->getDeclaringTrait()?->getName() !== InteractsWithSchemas::class)) {
                    return null;
                }
            }
        }

        $changed = false;

        foreach ($node->getArgs() as $argument) {
            if ($argument->name?->toString() === 'skipComponentsChildContainersWhileSearching') {
                $argument->name = new Identifier('skipComponentsChildSchemasWhileSearching');
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }
}
