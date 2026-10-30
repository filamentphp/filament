<?php

namespace Filament\Upgrade\Rector;

use Filament\Upgrade\Support\PropertyRenameGuard;
use PhpParser\Node;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\VarLikeIdentifier;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Rector\Renaming\ValueObject\RenameProperty;

class RenameApiPropertiesRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<RenameProperty>
     */
    private array $renames = [];

    public function __construct(private PropertyRenameGuard $propertyRenameGuard) {}

    public function getNodeTypes(): array
    {
        return [Property::class, PropertyFetch::class, NullsafePropertyFetch::class, StaticPropertyFetch::class];
    }

    /**
     * @param  Property | PropertyFetch | NullsafePropertyFetch | StaticPropertyFetch  $node
     */
    public function refactor(Node $node): ?Node
    {
        $context = ScopeFetcher::fetch($node)->getClassReflection();

        if ($node instanceof Property) {
            if (! $context) {
                return null;
            }

            $changed = false;

            foreach ($node->props as $property) {
                foreach ($this->renames as $rename) {
                    if (($property->name->toString() === $rename->getOldProperty()) && $this->propertyRenameGuard->canRename($context, $rename->getObjectType()->getClassName(), $rename->getOldProperty(), $rename->getNewProperty(), $this->getFile()->getFilePath())) {
                        $property->name = new VarLikeIdentifier($rename->getNewProperty());
                        $changed = true;
                    }
                }
            }

            return $changed ? $node : null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $receiver = $node instanceof StaticPropertyFetch ? $node->class : $node->var;
        $isRelative = ($receiver instanceof Name && in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'], true))
            || ($context?->isTrait() && ($receiver instanceof Variable) && ($receiver->name === 'this'));
        $type = $isRelative && $context ? new ObjectType($context->getName()) : TypeCombinator::removeNull($this->getType($receiver));

        if (($node instanceof StaticPropertyFetch) && $type->isClassString()->yes()) {
            $type = $type->getClassStringObjectType();
        }

        foreach ($this->renames as $rename) {
            if ($node->name->toString() !== $rename->getOldProperty()) {
                continue;
            }

            if ((! ($isRelative && $context?->isTrait())) && (! $rename->getObjectType()->isSuperTypeOf($type)->yes())) {
                continue;
            }

            $classes = $type->getObjectClassReflections();

            if (! $classes) {
                continue;
            }

            $canRename = true;

            foreach ($classes as $class) {
                if (! $this->propertyRenameGuard->canRename($class, $rename->getObjectType()->getClassName(), $rename->getOldProperty(), $rename->getNewProperty(), $this->getFile()->getFilePath())) {
                    $canRename = false;

                    break;
                }
            }

            if (! $canRename) {
                continue;
            }

            $node->name = $node instanceof StaticPropertyFetch ? new VarLikeIdentifier($rename->getNewProperty()) : new Identifier($rename->getNewProperty());

            return $node;
        }

        return null;
    }

    /**
     * @param  array<RenameProperty>  $configuration
     */
    public function configure(array $configuration): void
    {
        $this->renames = $configuration;
    }
}
