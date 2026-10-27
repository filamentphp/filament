<?php

namespace Filament\Upgrade\Support;

use PhpParser\Node;
use PhpParser\Node\UnionType;
use PhpParser\NodeFinder;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\Type\Type;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocNodeFinder\PhpDocNodeByTypeFinder;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\StaticTypeMapper\StaticTypeMapper;

class UnnormalizedUnionTypeResolver
{
    public function __construct(
        private NodeTypeResolver $nodeTypeResolver,
        private PhpDocInfoFactory $phpDocInfoFactory,
        private PhpDocNodeByTypeFinder $phpDocNodeByTypeFinder,
        private StaticTypeMapper $staticTypeMapper,
    ) {}

    /**
     * @param  array<Node>  $nodes
     * @return iterable<array<Type>>
     */
    public function resolve(array $nodes): iterable
    {
        foreach ((new NodeFinder)->find($nodes, static fn (Node $node): bool => ($node instanceof UnionType) || ($node->getDocComment() !== null)) as $node) {
            if ($node instanceof UnionType) {
                yield array_map(fn (Node $type): Type => $this->nodeTypeResolver->getType($type), $node->types);
            }

            $phpDoc = $this->phpDocInfoFactory->createFromNode($node);

            if (! $phpDoc) {
                continue;
            }

            foreach ($this->phpDocNodeByTypeFinder->findByType($phpDoc->getPhpDocNode(), UnionTypeNode::class) as $union) {
                yield array_map(fn ($type): Type => $this->staticTypeMapper->mapPHPStanPhpDocTypeNodeToPHPStanType($type, $node), $union->types);
            }
        }
    }
}
