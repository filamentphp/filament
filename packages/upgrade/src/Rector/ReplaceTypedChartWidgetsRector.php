<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceTypedChartWidgetsRector extends AbstractRector
{
    public function __construct(private AstResolver $astResolver) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace typed chart widget parents while preserving the inherited chart type', [
            new CodeSample(
                'class SalesChart extends \\Filament\\Widgets\\BarChartWidget {}',
                <<<'PHP'
                    class SalesChart extends \Filament\Widgets\ChartWidget
                    {
                        protected function getType(): string
                        {
                            return 'bar';
                        }
                    }
                    PHP,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->extends) {
            return null;
        }

        $type = match (strtolower((string) $this->getName($node->extends))) {
            'filament\\widgets\\barchartwidget' => 'bar',
            'filament\\widgets\\bubblechartwidget' => 'bubble',
            'filament\\widgets\\doughnutchartwidget' => 'doughnut',
            'filament\\widgets\\linechartwidget' => 'line',
            'filament\\widgets\\piechartwidget' => 'pie',
            'filament\\widgets\\polarareachartwidget' => 'polarArea',
            'filament\\widgets\\radarchartwidget' => 'radar',
            'filament\\widgets\\scatterchartwidget' => 'scatter',
            default => null,
        };

        if ($type === null) {
            return null;
        }

        $context = ScopeFetcher::fetch($node)->getClassReflection();

        // Shared traits must not be specialized for one consumer's parent type.
        if (($node->getTraitUses() !== []) && ((! $context) || (! $this->canPreserveTraits($node)))) {
            return null;
        }

        $hasUnsupportedParentReference = false;
        $this->traverseNodesWithCallable($node->stmts, function (Node $candidate) use (&$hasUnsupportedParentReference): ?int {
            if ($candidate instanceof ClassLike) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            if ($this->isParentReference($candidate) && (! $this->isSupportedParentCall($candidate))) {
                $hasUnsupportedParentReference = true;
            }

            return null;
        });

        if ($hasUnsupportedParentReference) {
            return null;
        }

        $this->traverseNodesWithCallable($node->stmts, function (Node $candidate) use ($type): Node | int | null {
            if ($candidate instanceof ClassLike) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            return $this->isSupportedParentCall($candidate) ? new String_($type) : null;
        });

        $parentName = $this->getName($node->extends);
        $node->extends = new FullyQualified('Filament\\Widgets\\ChartWidget');

        // A concrete trait method (including an alias) takes precedence over inheritance.
        $hasTraitType = $context?->hasNativeMethod('getType')
            && (strcasecmp($context->getNativeMethod('getType')->getDeclaringClass()->getName(), $parentName) !== 0)
            && (! $context->getNativeMethod('getType')->isAbstract());

        if ((! $node->getMethod('getType')) && (! $hasTraitType)) {
            $node->stmts[] = new ClassMethod('getType', [
                'flags' => Class_::MODIFIER_PROTECTED,
                'returnType' => new Identifier('string'),
                'stmts' => [new Return_(new String_($type))],
            ]);
        }

        return $node;
    }

    private function canPreserveTraits(ClassLike $node): bool
    {
        foreach ($node->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $traitName) {
                $name = $this->getName($traitName);
                $traitNode = $name ? $this->astResolver->resolveClassFromName($name) : null;

                if ((! $traitNode) || (! $this->canPreserveTraits($traitNode))) {
                    return false;
                }

                $hasParentReference = false;
                $this->traverseNodesWithCallable($traitNode->stmts, function (Node $candidate) use (&$hasParentReference): ?int {
                    if ($candidate instanceof ClassLike) {
                        return NodeTraverser::DONT_TRAVERSE_CHILDREN;
                    }

                    if ($this->isParentReference($candidate)) {
                        $hasParentReference = true;
                    }

                    return null;
                });

                if ($hasParentReference) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isParentReference(Node $node): bool
    {
        if ($node instanceof String_) {
            return in_array(strtolower($node->value), ['parent', 'parent::gettype'], true);
        }

        return (($node instanceof StaticCall) || ($node instanceof ClassConstFetch))
            && ($node->class instanceof Name)
            && $this->isName($node->class, 'parent')
            && (($node instanceof ClassConstFetch) || (! $node->name instanceof Identifier) || $this->isName($node->name, 'getType'));
    }

    private function isSupportedParentCall(Node $node): bool
    {
        return ($node instanceof StaticCall)
            && ($node->class instanceof Name)
            && $this->isName($node->class, 'parent')
            && ($node->name instanceof Identifier)
            && $this->isName($node->name, 'getType')
            && (! $node->isFirstClassCallable())
            && ($node->getArgs() === []);
    }
}
