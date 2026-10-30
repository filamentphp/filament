<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure as ClosureExpression;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\UnionType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class ReplaceDeprecatedBehavioralConfigurationRector extends AbstractRector
{
    /** @var array<string, string> */
    private const NEGATIONS = [
        'disablepreview' => 'previewable',
        'disableitemcreation' => 'addable',
        'disableitemdeletion' => 'deletable',
        'disableitemmovement' => 'reorderable',
        'disableaddingrows' => 'addable',
        'disabledeletingrows' => 'deletable',
        'disableeditingkeys' => 'editableKeys',
        'disableeditingvalues' => 'editableValues',
        'disableautocomplete' => 'autocomplete',
        'disableautocapitalize' => 'autocapitalize',
        'disableplaceholderselection' => 'selectablePlaceholder',
        'withoutdate' => 'date',
        'withoutseconds' => 'seconds',
        'withouttime' => 'time',
        'disablecreateanother' => 'createAnother',
        'disableattachanother' => 'attachAnother',
        'disableassociateanother' => 'associateAnother',
    ];

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /** @param MethodCall | NullsafeMethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        $method = strtolower($node->name->toString());

        $receiver = $node;
        $hasNullsafeAccess = false;

        while ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall || $receiver instanceof PropertyFetch || $receiver instanceof NullsafePropertyFetch) {
            $hasNullsafeAccess = $hasNullsafeAccess || ($receiver instanceof NullsafeMethodCall) || ($receiver instanceof NullsafePropertyFetch);
            $receiver = $receiver->var;
        }

        $factory = $node->var;

        // Follow ordinary fluent configuration back to its factory or typed owner.
        while ($factory instanceof MethodCall || $factory instanceof NullsafeMethodCall) {
            if ((! $factory->name instanceof Identifier) || $factory->isFirstClassCallable()) {
                return null;
            }

            $factory = $factory->var;
        }

        if ($factory instanceof StaticCall && $factory->class instanceof Name && $this->isName($factory->name, 'make') && (! $factory->isFirstClassCallable())) {
            $class = $this->getName($factory->class);
        } else {
            $type = TypeCombinator::removeNull($this->getNativeType($factory));
            $classes = $type->getObjectClassNames();

            if (count($classes) !== 1) {
                return null;
            }

            $class = $classes[0];
        }

        $methods = match ($class) {
            BaseFileUpload::class, FileUpload::class => ['disablepreview', ...($class === FileUpload::class ? ['imagecropaspectratio'] : [])],
            Builder::class, Repeater::class => ['disableitemcreation', 'disableitemdeletion', 'disableitemmovement', 'inset'],
            KeyValue::class => ['disableaddingrows', 'disabledeletingrows', 'disableeditingkeys', 'disableeditingvalues'],
            TextInput::class => ['disableautocomplete', 'disableautocapitalize'],
            Textarea::class => ['disableautocomplete'],
            Select::class => ['disableplaceholderselection'],
            DateTimePicker::class, DatePicker::class, TimePicker::class => ['withoutdate', 'withoutseconds', 'withouttime', 'icon'],
            Action::class => ['centermodal'],
            CreateAction::class => ['disablecreateanother', 'centermodal'],
            AttachAction::class => ['disableattachanother', 'centermodal'],
            AssociateAction::class => ['disableassociateanother', 'centermodal'],
            default => [],
        };

        if (in_array($class, [Builder::class, Repeater::class], true) && $node->var instanceof NullsafeMethodCall && $this->isName($node->var->name, 'inset')) {
            $receiver = $this->refactor($node->var);

            if ($receiver instanceof Expr) {
                return new NullsafeMethodCall($receiver, $node->name, $node->args);
            }
        }

        if ((! in_array($method, $methods, true)) || (count($node->args) > 1)) {
            return null;
        }

        $parameter = match ($method) {
            'imagecropaspectratio' => 'ratio',
            'icon' => 'icon',
            default => 'condition',
        };
        $argument = $node->getArgs()[0] ?? null;

        if ($argument && ($argument->unpack || ($argument->name && ($argument->name->toString() !== $parameter)))) {
            return null;
        }

        if ($method === 'inset') {
            return $node->var;
        }

        // Renaming preserves nullsafe short-circuiting for ordinary setters.
        // Multi-call adapters below require an ordinary receiver.
        if ($hasNullsafeAccess && in_array($method, ['imagecropaspectratio', 'icon', 'centermodal'], true)) {
            return null;
        }

        $value = $argument->value ?? new ConstFetch(new Name(($method === 'icon') ? 'null' : 'true'));
        $literal = ($value instanceof ConstFetch) ? strtolower($value->name->toString()) : null;

        if ($method === 'imagecropaspectratio') {
            if (! $argument) {
                return null;
            }

            if ((! $value instanceof String_) && ($literal !== 'null')) {
                if (! TypeCombinator::union(new StringType, new ObjectType(Closure::class), new NullType)->isSuperTypeOf($this->getNativeType($value))->yes()) {
                    return null;
                }

                $wrapper = new ArrowFunction([
                    'static' => true,
                    'params' => [
                        new Param(new Variable('component'), type: new FullyQualified(FileUpload::class)),
                        new Param(new Variable('ratio'), type: new UnionType([new Identifier('string'), new FullyQualified(Closure::class), new Identifier('null')])),
                    ],
                    'returnType' => new FullyQualified(FileUpload::class),
                    'expr' => new MethodCall(new MethodCall(new Variable('component'), 'imageAspectRatio', [new Arg(new Variable('ratio'))]), 'automaticallyCropImagesToAspectRatio', [new Arg(new Ternary(
                        new Instanceof_(new Variable('ratio'), new FullyQualified(Closure::class)),
                        new Variable('ratio'),
                        new FuncCall(new FullyQualified('filled'), [new Arg(new Variable('ratio'))]),
                    ))]),
                ]);

                return new FuncCall($wrapper, [new Arg($node->var), new Arg($value)]);
            }

            $ratio = ($value instanceof String_) ? $value->value : null;

            return new MethodCall(new MethodCall($node->var, 'imageAspectRatio', [new Arg($value)]), 'automaticallyCropImagesToAspectRatio', [new Arg(new ConstFetch(new Name((trim($ratio ?? '') !== '') ? 'true' : 'false')))]);
        }

        if ($method === 'icon') {
            if ($literal === 'false') {
                return $node->var;
            }

            if ((! $value instanceof String_) && ($literal !== 'null')) {
                if (! TypeCombinator::union(new StringType, new ObjectType('BackedEnum'), new ObjectType('Illuminate\\Contracts\\Support\\Htmlable'), new ObjectType(Closure::class), new ConstantBooleanType(false), new NullType)->isSuperTypeOf($this->getNativeType($value))->yes()) {
                    return null;
                }

                return new FuncCall(new ArrowFunction([
                    'static' => true,
                    'params' => [
                        new Param(new Variable('component'), type: new FullyQualified($class)),
                        new Param(new Variable('icon'), type: new UnionType([new Identifier('string'), new FullyQualified('BackedEnum'), new FullyQualified('Illuminate\\Contracts\\Support\\Htmlable'), new FullyQualified(Closure::class), new Identifier('bool'), new Identifier('null')])),
                    ],
                    'returnType' => new FullyQualified($class),
                    'expr' => new Ternary(
                        new Identical(new Variable('icon'), new ConstFetch(new Name('false'))),
                        new Variable('component'),
                        new MethodCall(new Variable('component'), 'suffixIcon', [
                            new Arg(new Coalesce(new Variable('icon'), new ClassConstFetch(new FullyQualified('Filament\\Support\\Icons\\Heroicon'), 'Calendar'))),
                            new Arg(new ConstFetch(new Name('true')), name: new Identifier('isInline')),
                        ]),
                    ),
                ]), [new Arg($node->var), new Arg($value)]);
            }

            return new MethodCall($node->var, 'suffixIcon', [
                new Arg(($literal === 'null') ? new ClassConstFetch(new FullyQualified('Filament\\Support\\Icons\\Heroicon'), 'Calendar') : $value),
                new Arg(new ConstFetch(new Name('true')), name: new Identifier('isInline')),
            ]);
        }

        if (isset(self::NEGATIONS[$method]) && (! in_array($literal, ['true', 'false'], true)) && TypeCombinator::union(new BooleanType, new ObjectType(Closure::class))->isSuperTypeOf($this->getNativeType($value))->yes()) {
            $isAction = in_array($class, [CreateAction::class, AttachAction::class, AssociateAction::class], true);
            $isAutocomplete = in_array($method, ['disableautocomplete', 'disableautocapitalize'], true);
            $owner = match (true) {
                $isAutocomplete => 'Filament\\Forms\\Components\\Field',
                in_array($class, [BaseFileUpload::class, FileUpload::class], true) => BaseFileUpload::class,
                in_array($class, [DateTimePicker::class, DatePicker::class, TimePicker::class], true) => DateTimePicker::class,
                default => $class,
            };
            $ownerVariable = $isAction ? 'action' : 'component';
            $evaluation = new MethodCall(new Variable($ownerVariable), 'evaluate', [new Arg(new Variable('condition'))]);
            $wrapper = new ArrowFunction([
                'static' => true,
                'params' => [new Param(new Variable('condition'), type: new UnionType([new Identifier('bool'), new FullyQualified(Closure::class)]))],
                'returnType' => new FullyQualified(Closure::class),
                'expr' => new ArrowFunction([
                    'static' => true,
                    'params' => [new Param(new Variable($ownerVariable), type: new FullyQualified($owner))],
                    'returnType' => $isAutocomplete ? new NullableType(new Identifier('bool')) : new Identifier('bool'),
                    'expr' => $isAutocomplete ? new Ternary($evaluation, new ConstFetch(new Name('false')), new ConstFetch(new Name('null'))) : new BooleanNot($evaluation),
                ]),
            ]);

            $node->name = new Identifier(self::NEGATIONS[$method]);
            $node->args = [new Arg(new FuncCall($wrapper, [new Arg($value)]))];

            return $node;
        }

        if (($method === 'centermodal') && (! in_array($literal, ['true', 'false', 'null'], true)) && TypeCombinator::union(new BooleanType, new ObjectType(Closure::class), new NullType)->isSuperTypeOf($this->getNativeType($value))->yes()) {
            return new FuncCall(new ClosureExpression([
                'static' => true,
                'params' => [
                    new Param(new Variable('action'), type: new FullyQualified(Action::class)),
                    new Param(new Variable('condition'), type: new UnionType([new Identifier('bool'), new FullyQualified(Closure::class), new Identifier('null')])),
                ],
                'returnType' => new FullyQualified(Action::class),
                'stmts' => [
                    new If_(new MethodCall(new Variable('action'), 'evaluate', [new Arg(new Variable('condition'))]), ['stmts' => [new Expression(new MethodCall(new Variable('action'), 'modalAlignment', [new Arg(new ClassConstFetch(new FullyQualified('Filament\\Support\\Enums\\Alignment'), 'Center'))]))]]),
                    new Return_(new Variable('action')),
                ],
            ]), [new Arg($node->var), new Arg($value)]);
        }

        if ((! in_array($literal, ['true', 'false'], true)) && (($method !== 'centermodal') || ($literal !== 'null'))) {
            return null;
        }

        if ($method === 'centermodal') {
            return ($literal === 'true')
                ? new MethodCall($node->var, 'modalAlignment', [new Arg(new ClassConstFetch(new FullyQualified('Filament\\Support\\Enums\\Alignment'), 'Center'))])
                : $node->var;
        }

        $replacement = self::NEGATIONS[$method];
        $result = (($literal === 'false') && in_array($replacement, ['autocomplete', 'autocapitalize'], true)) ? 'null' : (($literal === 'true') ? 'false' : 'true');

        $node->name = new Identifier($replacement);
        $node->args = [new Arg(new ConstFetch(new Name($result)))];

        return $node;
    }
}
