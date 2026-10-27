<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\Cast\String_ as StringCast;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\UnaryPlus;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceDeprecatedNumberHelpersRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace literal-only number helper calls, preserving division and string results', [
            new CodeSample(
                "\\Filament\\Support\\format_money(123450, 'USD', 100);",
                "(string) \\Illuminate\\Support\\Number::currency(123450 / 100, 'USD');",
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Name) || $node->isFirstClassCallable()) {
            return null;
        }

        $isMoney = $this->isName($node->name, 'Filament\\Support\\format_money');

        if ((! $isMoney) && (! $this->isName($node->name, 'Filament\\Support\\format_number'))) {
            return null;
        }

        foreach ((new NodeFinder)->findInstanceOf($this->getFile()->getNewStmts(), Function_::class) as $function) {
            if ($function->namespacedName && $this->isNames($function->namespacedName, ['Filament\\Support\\format_money', 'Filament\\Support\\format_number'])) {
                return null;
            }
        }

        $parameters = $isMoney ? ['money', 'currency', 'divideBy'] : ['number'];
        $arguments = [];

        foreach ($node->getArgs() as $index => $argument) {
            if ($argument->unpack || $argument->byRef) {
                return null;
            }

            $parameter = $argument->name?->toString() ?? ($parameters[$index] ?? null);

            if (($parameter === null) || (! in_array($parameter, $parameters, true)) || isset($arguments[$parameter])) {
                return null;
            }

            $arguments[$parameter] = $argument->value;
        }

        $number = $arguments[$parameters[0]] ?? null;

        // Literal-only inputs avoid changing weak/strict argument coercion,
        // argument evaluation order, side effects, or ignored extra arguments.
        if ((! $number instanceof Expr) || (! $this->isNumericLiteral($number))) {
            return null;
        }

        $replacementArguments = [new Arg($number)];

        if ($isMoney) {
            $currency = $arguments['currency'] ?? null;
            $divisor = $arguments['divideBy'] ?? new Int_(0);

            if ((! $currency instanceof String_) || (! $this->isNumericLiteral($divisor, integerOnly: true))) {
                return null;
            }

            $divisorValue = ($divisor instanceof UnaryMinus || $divisor instanceof UnaryPlus) ? $divisor->expr : $divisor;

            if ($divisorValue instanceof Int_ && ($divisorValue->value !== 0)) {
                $replacementArguments[0] = new Arg(new Div($number, $divisor));
            }

            $replacementArguments[] = new Arg($currency);
        }

        // The helpers declared `string` in a weakly typed file; Laravel may
        // return `false`. Preserve that coercion even in strict callers.
        return new StringCast(new StaticCall(
            new FullyQualified('Illuminate\\Support\\Number'),
            $isMoney ? 'currency' : 'format',
            $replacementArguments,
        ));
    }

    protected function isNumericLiteral(Expr $expression, bool $integerOnly = false): bool
    {
        if ($expression instanceof UnaryMinus || $expression instanceof UnaryPlus) {
            $expression = $expression->expr;
        }

        return ($expression instanceof Int_) || ((! $integerOnly) && ($expression instanceof Float_));
    }
}
