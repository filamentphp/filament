<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Reflection\ClassReflection;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AddInterfaceByTraitRector
 */
class AddTraitByTraitRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, string>
     */
    private array $traitByTrait = [];

    public function __construct(
        protected readonly TestsNodeAnalyzer $testsNodeAnalyzer,
        protected readonly ReflectionResolver $reflectionResolver,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add trait by used trait', []);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $scope = ScopeFetcher::fetch($node);

        $classReflection = $scope->getClassReflection();

        if (! ($classReflection instanceof ClassReflection)) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->traitByTrait as $traitName => $newTraitName) {
            if (! $classReflection->hasTraitUse($traitName)) {
                continue;
            }

            if ($classReflection->hasTraitUse($newTraitName)) {
                continue;
            }

            foreach ($node->stmts as $stmt) {
                if (! ($stmt instanceof TraitUse)) {
                    continue;
                }

                foreach ($stmt->traits as $trait) {
                    if ($this->isName($trait, $newTraitName)) {
                        break 3;
                    }
                }
            }

            $traitUse = new TraitUse([new FullyQualified($newTraitName)]);
            $node->stmts = array_merge([$traitUse], $node->stmts);

            $hasChanged = true;
        }

        if (! $hasChanged) {
            return null;
        }

        return $node;
    }

    /**
     * @param  array<string, string>  $configuration
     */
    public function configure(array $configuration): void
    {
        $this->traitByTrait = $configuration;
    }
}
