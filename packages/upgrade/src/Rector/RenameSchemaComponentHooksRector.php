<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Upgrade\Support\UnnormalizedUnionTypeResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeTraverser;
use Rector\Configuration\RenamedClassesDataCollector;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameSchemaComponentHooksRector extends AbstractRector
{
    public function __construct(
        private AstResolver $astResolver,
        private RenamedClassesDataCollector $renamedClassesDataCollector,
        private UnnormalizedUnionTypeResolver $unnormalizedUnionTypeResolver,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename Filament schema component hooks and schema lookup named arguments', [
            new CodeSample('Importer::getOptionsFormComponents();', 'Importer::getOptionsSchemaComponents();'),
            new CodeSample('$schema->getComponent("name", skipComponentsChildContainersWhileSearching: []);', '$schema->getComponent("name", skipComponentsChildSchemasWhileSearching: []);'),
        ]);
    }

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

            if ((! $context) || $context->isTrait() || (! $this->supports(new ObjectType($context->getName()), $oldName)) || (! $this->canRename($context, $oldName, $newName))) {
                return null;
            }
        } else {
            $receiver = $node instanceof StaticCall ? $node->class : $node->var;
            $type = TypeCombinator::removeNull($this->getType($receiver));

            if (($node instanceof StaticCall) && $type->isClassString()->yes()) {
                $type = $type->getClassStringObjectType();
            }

            $type = TypeTraverser::map($type, function (Type $type, callable $traverse): Type {
                return $type instanceof ObjectType ? ($this->renamedClassesDataCollector->matchClassName($type) ?? $type) : $traverse($type);
            });

            if ($receiver instanceof Name && in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'], true)) {
                $context = ScopeFetcher::fetch($node)->getClassReflection();

                // A parent call inside a dual-name override must be reconciled manually too.
                if ((! $context) || (! $this->canRename($context, $oldName, $newName))) {
                    return null;
                }

                $receiverClass = strtolower($receiver->toString()) === 'parent' ? $this->getParentClass($context) : $context;

                if (! $receiverClass) {
                    return null;
                }

                $type = new ObjectType($receiverClass->getName());
            }

            if (! $this->supports($type, $oldName)) {
                return null;
            }

            foreach ($type->getObjectClassReflections() as $classReflection) {
                if (! $this->canRename($classReflection, $oldName, $newName)) {
                    return null;
                }
            }

            if (($node instanceof StaticCall) && ($oldName === 'getformschema')) {
                if (! $this->isInstanceContextCall($node, $type)) {
                    return null;
                }
            }
        }

        // PHPStan normalizes `Base | CustomBase` to `Base`. Do not lose a custom
        // dispatch boundary hidden by that normalization.
        foreach ($this->unnormalizedUnionTypeResolver->resolve($this->getFile()->getNewStmts()) as $types) {
            foreach ($types as $type) {
                if ($type->isClassString()->yes()) {
                    $type = $type->getClassStringObjectType();
                }

                foreach ($type->getObjectClassReflections() as $classReflection) {
                    if ($this->supports(new ObjectType($classReflection->getName()), $oldName) && (! $this->canRename($classReflection, $oldName, $newName))) {
                        return null;
                    }
                }
            }
        }

        $node->name = new Identifier($newName);

        return $node;
    }

    private function supports(Type $type, string $methodName): bool
    {
        $owners = $methodName === 'getformschema' ? [Operator::class] : [Importer::class, Exporter::class];
        $ownerType = TypeCombinator::union(...array_map(static fn (string $owner): ObjectType => new ObjectType($owner), $owners));
        $supportedType = new NeverType;

        foreach ($type->getObjectClassReflections() as $classReflection) {
            for ($ancestor = $classReflection; $ancestor; $ancestor = $this->getParentClass($ancestor)) {
                if ($ownerType->isSuperTypeOf(new ObjectType($ancestor->getName()))->yes()) {
                    $supportedType = TypeCombinator::union($supportedType, new ObjectType($classReflection->getName()));

                    break;
                }
            }
        }

        return $supportedType->isSuperTypeOf($type)->yes();
    }

    private function getParentClass(ClassReflection $classReflection): ?ClassReflection
    {
        if ($parent = $classReflection->getParentClass()) {
            return $parent;
        }

        // The registered class rename can resolve an extracted query-builder
        // parent even when its legacy class is no longer installed.
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

    private function canRename(ClassReflection $classReflection, string $oldName, string $newName): bool
    {
        // A local override must not migrate while its custom parent call is
        // left behind in a different file.
        if ($parent = $this->getParentClass($classReflection)) {
            if (! $this->canRename($parent, $oldName, $newName)) {
                return false;
            }
        }

        foreach ([$oldName, $newName] as $name) {
            if (! $classReflection->hasNativeMethod($name)) {
                continue;
            }

            $method = $classReflection->getNativeMethod($name);
            $declaringClass = $method->getDeclaringClass();
            $declaringName = $declaringClass->getName();

            if (in_array($declaringName, [Importer::class, Exporter::class, Operator::class], true) || str_starts_with($declaringName, 'Filament\\QueryBuilder\\Constraints\\')) {
                continue;
            }

            // Migrate an old-only hook with its local references, never overwrite
            // an existing canonical implementation or a trait adaptation.
            if (($name === $newName) || ($declaringClass->getFileName() !== $this->getFile()->getFilePath()) || (method_exists($method, 'getDeclaringTrait') && $method->getDeclaringTrait())) {
                return false;
            }

            $declaration = $this->astResolver->resolveClassFromClassReflection($declaringClass);
            $methodNode = $declaration instanceof Class_ ? $declaration->getMethod($oldName) : null;

            if (! $methodNode) {
                return false;
            }

            // An old hook forwarding to the new one could become recursive.
            if ((new NodeFinder)->findFirst($methodNode->stmts ?? [], static fn (Node $candidate): bool => ($candidate instanceof MethodCall || $candidate instanceof NullsafeMethodCall || $candidate instanceof StaticCall) && $candidate->name instanceof Identifier && (strtolower($candidate->name->toString()) === strtolower($newName)))) {
                return false;
            }
        }

        return true;
    }

    private function isInstanceContextCall(StaticCall $node, Type $receiverType): bool
    {
        $scope = ScopeFetcher::fetch($node);
        $function = $scope->getFunction();

        if ((! $node->class instanceof Name) || (! $function instanceof MethodReflection) || $function->isStatic()) {
            return false;
        }

        for ($context = $scope->getClassReflection(); $context; $context = $this->getParentClass($context)) {
            if ($receiverType->isSuperTypeOf(new ObjectType($context->getName()))->yes()) {
                return true;
            }
        }

        return false;
    }

    private function renameLookupArgument(MethodCall | NullsafeMethodCall | StaticCall $node, string $methodName): ?Node
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        if ($node instanceof StaticCall) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $type = $this->getType($node->class);

            if ($node->class instanceof Name && in_array(strtolower($node->class->toString()), ['parent', 'self', 'static'], true)) {
                $receiverClass = ($context && (strtolower($node->class->toString()) === 'parent')) ? $this->getParentClass($context) : $context;

                if (! $receiverClass) {
                    return null;
                }

                $type = new ObjectType($receiverClass->getName());
            }

            if (! $this->isInstanceContextCall($node, $type)) {
                return null;
            }
        } else {
            $type = TypeCombinator::removeNull($this->getType($node->var));
        }

        $owner = $methodName === 'getschemacomponent' ? HasSchemas::class : Schema::class;

        if (! (new ObjectType($owner))->isSuperTypeOf($type)->yes()) {
            return null;
        }

        $classReflections = $type->getObjectClassReflections();

        // Retain custom parameter contracts hidden by normalized unions.
        foreach ($this->unnormalizedUnionTypeResolver->resolve($this->getFile()->getNewStmts()) as $types) {
            foreach ($types as $alternativeType) {
                if ((new ObjectType($owner))->isSuperTypeOf($alternativeType)->yes()) {
                    array_push($classReflections, ...$alternativeType->getObjectClassReflections());
                }
            }
        }

        foreach ($classReflections as $classReflection) {
            if (! $classReflection->hasNativeMethod($methodName)) {
                return null;
            }

            $method = $classReflection->getNativeMethod($methodName);

            if (in_array($method->getDeclaringClass()->getName(), [Schema::class, HasSchemas::class], true)) {
                continue;
            }

            if (($owner !== HasSchemas::class) || (! method_exists($method, 'getDeclaringTrait')) || ($method->getDeclaringTrait()?->getName() !== InteractsWithSchemas::class)) {
                return null;
            }
        }

        foreach ($node->getArgs() as $argument) {
            if ($argument->unpack || ($argument->name?->toString() === 'skipComponentsChildSchemasWhileSearching')) {
                return null;
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
