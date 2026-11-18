<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\UnaryPlus;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;

class ReplaceDeprecatedNumberHelpersRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Name) {
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

        if ($node->isFirstClassCallable()) {
            return null;
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

        if ((! $number instanceof Expr) || ($number instanceof String_) || ($number instanceof Expr\ConstFetch) || ($number instanceof Expr\Array_)) {
            return null;
        }

        // Reordering named arguments must not reorder side effects.
        if (array_keys($arguments) !== array_values(array_intersect($parameters, array_keys($arguments)))) {
            foreach ($arguments as $value) {
                if ((! $value instanceof Variable) && (! $value instanceof String_) && (! $this->isNumericLiteral($value))) {
                    return null;
                }
            }
        }

        $replacementArguments = [new Arg($number)];

        if ($isMoney) {
            $currency = $arguments['currency'] ?? null;
            $divisor = $arguments['divideBy'] ?? new Int_(0);

            if ((! $currency) || ($currency instanceof Int_) || ($currency instanceof Float_) || ($currency instanceof Expr\ConstFetch) || ($currency instanceof Expr\Array_) || ((! $this->isNumericLiteral($divisor, integerOnly: true)) && ((! $divisor instanceof Variable) || (! $this->getNativeType($divisor)->isInteger()->yes())))) {
                return null;
            }

            if (($divisor instanceof Variable) && (((! $number instanceof Variable) && (! $this->isNumericLiteral($number))) || ((! $currency instanceof Variable) && (! $currency instanceof String_)))) {
                return null;
            }

            $divisorValue = ($divisor instanceof UnaryMinus || $divisor instanceof UnaryPlus) ? $divisor->expr : $divisor;

            if ($divisorValue instanceof Int_ && ($divisorValue->value !== 0)) {
                $replacementArguments[0] = new Arg(new Div($number, $divisor));
            } elseif ($divisor instanceof Variable) {
                $replacementArguments[0] = new Arg(new Ternary($divisor, new Div($number, $divisor), $number));
            }

            $replacementArguments[] = new Arg($currency);
        }

        return new StaticCall(
            new FullyQualified('Illuminate\\Support\\Number'),
            $isMoney ? 'currency' : 'format',
            $replacementArguments,
        );
    }

    protected function isNumericLiteral(Expr $expression, bool $integerOnly = false): bool
    {
        if ($expression instanceof UnaryMinus || $expression instanceof UnaryPlus) {
            $expression = $expression->expr;
        }

        return ($expression instanceof Int_) || ((! $integerOnly) && ($expression instanceof Float_));
    }
}
