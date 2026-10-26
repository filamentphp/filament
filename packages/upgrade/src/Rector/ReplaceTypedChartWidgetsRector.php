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
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceTypedChartWidgetsRector extends AbstractRector
{
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

        // Traits can supply `getType()` or calls to its parent implementation.
        if ($node->getTraitUses() !== []) {
            return null;
        }

        if ((new NodeFinder)->findFirst(
            $node->stmts,
            function (Node $candidate): bool {
                if ($candidate instanceof String_) {
                    return in_array(strtolower($candidate->value), ['parent', 'parent::gettype'], true);
                }

                return (($candidate instanceof StaticCall) || ($candidate instanceof ClassConstFetch)) &&
                    ($candidate->class instanceof Name) &&
                    $this->isName($candidate->class, 'parent') &&
                    (($candidate instanceof ClassConstFetch) || (! $candidate->name instanceof Identifier) || $this->isName($candidate->name, 'getType'));
            },
        )) {
            return null;
        }

        $node->extends = new FullyQualified('Filament\\Widgets\\ChartWidget');

        if (! $node->getMethod('getType')) {
            $node->stmts[] = new ClassMethod('getType', [
                'flags' => Class_::MODIFIER_PROTECTED,
                'returnType' => new Identifier('string'),
                'stmts' => [new Return_(new String_($type))],
            ]);
        }

        return $node;
    }
}
