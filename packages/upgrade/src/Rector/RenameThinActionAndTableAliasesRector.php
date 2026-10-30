<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Concerns\CanGroupRecords;
use Filament\Tables\Concerns\HasColumnManager;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenameThinActionAndTableAliasesRector extends AbstractRector
{
    /** @var array<string, array{class-string, string, list<string>}> */
    private const METHODS = [
        'mountedactionhasform' => [InteractsWithActions::class, 'mountedActionHasSchema', ['mountedAction']],
        'getmountedactionform' => [InteractsWithActions::class, 'getMountedActionSchema', ['actionNestingIndex', 'mountedAction']],
        'updatedtoggledtablecolumns' => [HasColumnManager::class, 'applyTableColumnManager', []],
        'gettoggledtablecolumnssessionkey' => [HasColumnManager::class, 'getTableColumnsSessionKey', []],
        'updatedtablegroupcolumn' => [CanGroupRecords::class, 'updatedTableGrouping', []],
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

        [$owner, $replacement, $parameters] = self::METHODS[$alias];

        if (count($node->args) > count($parameters)) {
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

            if ((! $class) || (! $this->owns($class, $owner, $alias))) {
                return null;
            }

            return new MethodCall(new Variable('this'), $replacement, $node->args);
        }

        $classes = TypeCombinator::removeNull($this->getType($node->var))->getObjectClassReflections();

        if ((! count($classes)) || array_filter($classes, fn (ClassReflection $class): bool => ! $this->owns($class, $owner, $alias))) {
            return null;
        }

        $node->name = new Identifier($replacement);

        return $node;
    }

    private function owns(ClassReflection $class, string $owner, string $alias): bool
    {
        if ($class->hasNativeMethod($alias)) {
            return false;
        }

        foreach ($class->getAncestors() as $ancestor) {
            if (($ancestor->getName() === $owner) || $ancestor->hasTraitUse($owner)) {
                return true;
            }
        }

        return false;
    }
}
