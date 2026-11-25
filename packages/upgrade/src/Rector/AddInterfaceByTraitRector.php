<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ClassReflection;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddInterfaceByTraitRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, string>
     */
    private array $interfaceByTrait = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add interface by used trait', []);
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
        $classReflection = ScopeFetcher::fetch($node)->getClassReflection();

        if (! ($classReflection instanceof ClassReflection)) {
            return null;
        }

        $interfacesToAdd = [];

        foreach ($this->interfaceByTrait as $traitName => $interfaceName) {
            if (! $classReflection->hasTraitUse($traitName)) {
                continue;
            }

            if ($classReflection->implementsInterface($interfaceName)) {
                continue;
            }

            $interfacesToAdd[$interfaceName] = new FullyQualified($interfaceName);
        }

        if ($interfacesToAdd === []) {
            return null;
        }

        $node->implements = array_merge($node->implements, array_values($interfacesToAdd));

        return $node;
    }

    /**
     * @param  array<string, string>  $configuration
     */
    public function configure(array $configuration): void
    {
        $this->interfaceByTrait = $configuration;
    }
}
