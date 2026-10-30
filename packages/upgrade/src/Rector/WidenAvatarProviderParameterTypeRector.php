<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;

class WidenAvatarProviderParameterTypeRector extends AbstractRector
{
    public function __construct(
        private StaticTypeMapper $staticTypeMapper,
    ) {}

    public function getNodeTypes(): array
    {
        return [Class_::class, Interface_::class];
    }

    /**
     * @param  Class_ | Interface_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node, new ObjectType('Filament\\AvatarProviders\\Contracts\\AvatarProvider'))) {
            return null;
        }

        $parameter = $node->getMethod('get')?->params[0] ?? null;

        if (! $parameter?->type) {
            return null;
        }

        $parameterType = $this->getType($parameter->type);
        $authenticatableType = new ObjectType('Illuminate\\Contracts\\Auth\\Authenticatable');

        if ($parameterType->isSuperTypeOf($authenticatableType)->yes()) {
            return null;
        }

        // Only widen the old contract, not arbitrary custom restrictions or
        // intersection types whose intended behavior requires manual review.
        if (! TypeCombinator::removeNull($parameterType)->equals(new ObjectType('Illuminate\\Database\\Eloquent\\Model'))) {
            return null;
        }

        $parameter->type = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode(
            TypeCombinator::union($parameterType, $authenticatableType),
            TypeKind::PARAM,
        );

        return $node;
    }
}
