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
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Support\Components\Component;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure as ClosureExpression;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Isset_;
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
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeFinder;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use ReflectionMethod;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ReplaceDeprecatedBehavioralConfigurationRector extends AbstractRector
{
    /** @var list<string> */
    private array $insetReservedVariables = [];

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace literal behavioral configuration on built-in component factories', [
            new CodeSample("FileUpload::make('photo')->imageCropAspectRatio('16:9')", "FileUpload::make('photo')->imageAspectRatio('16:9')->automaticallyCropImagesToAspectRatio(true)"),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, PropertyFetch::class, NullsafePropertyFetch::class, ArrayDimFetch::class, Isset_::class, Empty_::class, Coalesce::class];
    }

    /** @param Expr $node */
    public function refactor(Node $node): ?Node
    {
        $this->insetReservedVariables = [];

        if ($node instanceof Coalesce) {
            return $this->refactorInsetChain($node->left, static fn (Expr $expression): Expr => new Coalesce($expression, clone $node->right));
        }

        if ($node instanceof Empty_) {
            return $this->refactorInsetChain($node->expr, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('true')) : new Empty_($expression));
        }

        if ($node instanceof Isset_) {
            $result = null;
            $changed = false;

            foreach ($node->vars as $variable) {
                $replacement = $this->refactorInsetChain($variable, static fn (Expr $expression, bool $isNull): Expr => $isNull ? new ConstFetch(new Name('false')) : new Isset_([$expression]));
                $changed = $changed || ($replacement !== null);
                $replacement ??= new Isset_([$variable]);
                $result = $result ? new BooleanAnd($result, $replacement) : $replacement;
            }

            return $changed ? $result : null;
        }

        $insetReplacement = $this->refactorInsetChain($node);

        if ($insetReplacement) {
            return $insetReplacement;
        }

        if (! $node instanceof MethodCall) {
            return null;
        }

        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        $method = strtolower($node->name->toString());

        if ($method === 'inset') {
            return null;
        }

        $factory = $node->var;
        $precedingMethods = [];

        // Check the preceding methods' native fluent contracts after resolving
        // the owner, rather than limiting ordinary configuration to a whitelist.
        while ($factory instanceof MethodCall) {
            if ((! $factory->name instanceof Identifier) || $factory->isFirstClassCallable()) {
                return null;
            }

            $precedingMethods[] = strtolower($factory->name->toString());
            $factory = $factory->var;
        }

        if ($factory instanceof StaticCall && $factory->class instanceof Name && $this->isName($factory->name, 'make') && (! $factory->isFirstClassCallable())) {
            $class = $this->getName($factory->class);
        } else {
            $type = $this->getNativeType($factory);
            $classes = $type->getObjectClassNames();

            // Do not normalize a union hiding an overriding subclass into a
            // built-in receiver. PHPDoc alone is not a native type guarantee.
            if ((count($classes) !== 1) || (! $type->equals(new ObjectType($classes[0])))) {
                return null;
            }

            $class = $classes[0];

            if ((new NodeFinder)->findFirst($this->getFile()->getNewStmts(), function (Node $candidate) use ($class): bool {
                if (! $candidate instanceof UnionType) {
                    return false;
                }

                foreach ($candidate->types as $unionMember) {
                    $memberType = $this->getType($unionMember);

                    if ((! $memberType->equals(new ObjectType($class))) && (new ObjectType($class))->isSuperTypeOf($memberType)->yes()) {
                        return true;
                    }
                }

                return false;
            })) {
                return null;
            }
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

        if ((! in_array($method, $methods, true)) || (count($node->args) > 1)) {
            return null;
        }

        foreach ($precedingMethods as $precedingMethod) {
            if (in_array($precedingMethod, $methods, true)) {
                continue;
            }

            // A same-named macro is not a built-in fluent setter.
            if ((! method_exists($class, $precedingMethod)) || ((string) (new ReflectionMethod($class, $precedingMethod))->getReturnType() !== 'static')) {
                return null;
            }
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
            // Builder/repeater/placeholder wrappers captured the configuring
            // owner rather than evaluating on a later injected clone.
            $capturesOwner = in_array($class, [Builder::class, Repeater::class, Select::class], true);
            $isAction = in_array($class, [CreateAction::class, AttachAction::class, AssociateAction::class], true);
            $isAutocomplete = in_array($method, ['disableautocomplete', 'disableautocapitalize'], true);
            $owner = match (true) {
                $isAutocomplete => 'Filament\\Forms\\Components\\Field',
                in_array($class, [BaseFileUpload::class, FileUpload::class], true) => BaseFileUpload::class,
                in_array($class, [DateTimePicker::class, DatePicker::class, TimePicker::class], true) => DateTimePicker::class,
                default => $class,
            };
            $ownerVariable = $isAction ? 'action' : 'component';
            $evaluation = new MethodCall(new Variable($capturesOwner ? 'owner' : $ownerVariable), 'evaluate', [new Arg(new Variable('condition'))]);
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

            if ($capturesOwner) {
                array_unshift($wrapper->params, new Param(new Variable('owner'), type: new FullyQualified($class)));
                $wrapper->returnType = new FullyQualified($class);
                $wrapper->expr = new MethodCall(new Variable('owner'), self::NEGATIONS[$method], [new Arg($wrapper->expr)]);

                return new FuncCall($wrapper, [new Arg($node->var), new Arg($value)]);
            }

            return new MethodCall($node->var, self::NEGATIONS[$method], [new Arg(new FuncCall($wrapper, [new Arg($value)]))]);
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

        return new MethodCall($node->var, $replacement, [new Arg(new ConstFetch(new Name($result)))]);
    }

    private function refactorInsetChain(Expr $node, ?Closure $context = null): ?Expr
    {
        $chain = [];
        $candidate = $node;
        $hasInset = false;

        while ($candidate instanceof MethodCall || $candidate instanceof NullsafeMethodCall || $candidate instanceof PropertyFetch || $candidate instanceof NullsafePropertyFetch || $candidate instanceof ArrayDimFetch) {
            if (($candidate instanceof MethodCall || $candidate instanceof NullsafeMethodCall) && $this->isSupportedInsetCall($candidate)) {
                $hasInset = true;
            }

            $chain[] = $candidate;
            $candidate = $candidate->var;
        }

        return $hasInset ? $this->replaceInsetChain($candidate, array_reverse($chain), $context) : null;
    }

    /** @param array<MethodCall | NullsafeMethodCall | PropertyFetch | NullsafePropertyFetch | ArrayDimFetch> $chain */
    private function replaceInsetChain(Expr $receiver, array $chain, ?Closure $context): Expr
    {
        foreach ($chain as $index => $original) {
            $node = clone $original;

            if ($node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch) {
                $variable = new Variable($this->insetTemporary());
                $normal = $node instanceof NullsafeMethodCall
                    ? new MethodCall($node->var, $node->name, $node->args, $node->getAttributes())
                    : new PropertyFetch($node->var, $node->name, $node->getAttributes());

                // Keep the consuming context inside both branches, but read the
                // receiver normally so magic access and its warnings stay intact.
                return new Ternary(
                    new Identical(new Assign($variable, $receiver), new ConstFetch(new Name('null'))),
                    $context ? $context(new ConstFetch(new Name('null')), true) : new ConstFetch(new Name('null')),
                    $this->replaceInsetChain($variable, [$normal, ...array_slice($chain, $index + 1)], $context),
                );
            }

            if ($node instanceof MethodCall && $node->name instanceof Identifier && $this->isSupportedInsetCall($node)) {
                $receiver = $this->insetAdapter($receiver, $node->getArgs()[0] ?? null, $node->name->toString());

                continue;
            }

            $node->var = $receiver;
            $receiver = $node;
        }

        return $context ? $context($receiver, false) : $receiver;
    }

    private function isSupportedInsetCall(MethodCall | NullsafeMethodCall $call): bool
    {
        if ((! $call->name instanceof Identifier) || ! $this->isName($call->name, 'inset') || $call->isFirstClassCallable() || count($call->args) > 1) {
            return false;
        }

        $argument = $call->getArgs()[0] ?? null;

        return (! $argument || (! $argument->unpack && (! $argument->name || $this->isName($argument->name, 'condition'))))
            && $this->isInsetReceiver($call->var);
    }

    private function insetAdapter(Expr $receiver, ?Arg $argument, string $method): FuncCall
    {
        $callable = new FuncCall(new ArrowFunction([
            'static' => true,
            'params' => [new Param(new Variable('component'))],
            // A base-typed receiver or container binding may supply an actual
            // override. Resolve its callable before evaluating arguments, just
            // like PHP does for the original method call.
            'expr' => new Ternary(
                new BooleanAnd(
                    new BooleanOr(new Instanceof_(new Variable('component'), new FullyQualified(Builder::class)), new Instanceof_(new Variable('component'), new FullyQualified(Repeater::class))),
                    new BooleanNot(new FuncCall(new FullyQualified('method_exists'), [new Arg(new Variable('component')), new Arg(new String_('inset'))])),
                ),
                new ArrowFunction([
                    'static' => true,
                    'params' => [
                        new Param(new Variable('condition'), default: new ConstFetch(new Name('true')), type: new UnionType([new Identifier('bool'), new FullyQualified(Closure::class)])),
                    ],
                    'expr' => new Variable('component'),
                ]),
                new MethodCall(new Variable('component'), new String_($method), [new VariadicPlaceholder]),
            ),
        ]), [new Arg($receiver)]);

        return new FuncCall($callable, $argument ? [$argument] : []);
    }

    private function isInsetReceiver(Expr $receiver): bool
    {
        return $this->isInsetType($this->insetReceiverType($receiver));
    }

    private function isInsetType(Type $type): bool
    {
        $type = TypeCombinator::removeNull($type);

        if ($type->getObjectClassNames() && TypeCombinator::union(new ObjectType(Builder::class), new ObjectType(Repeater::class))->isSuperTypeOf($type)->yes()) {
            return true;
        }

        if (in_array($type->getObjectClassNames(), [[Field::class], [SchemaComponent::class], [Component::class]], true)) {
            return true;
        }

        return false;
    }

    private function insetReceiverType(Expr $receiver): Type
    {
        $type = TypeCombinator::removeNull($this->getNativeType($receiver));

        if ($this->isInsetType($type) || (! ($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall)) || (! $receiver->name instanceof Identifier) || $receiver->isFirstClassCallable()) {
            return $type;
        }

        $owner = $this->insetReceiverType($receiver->var);

        if (! $this->isInsetType($owner)) {
            return $type;
        }

        if ($this->isName($receiver->name, 'inset')) {
            return $owner;
        }

        foreach ($owner->getObjectClassReflections() as $class) {
            if (! $class->hasNativeMethod($receiver->name->toString())) {
                return $type;
            }

            foreach ($class->getNativeMethod($receiver->name->toString())->getVariants() as $variant) {
                if (! $variant->getNativeReturnType() instanceof StaticType) {
                    return $type;
                }
            }
        }

        return $owner;
    }

    private function insetTemporary(): string
    {
        $variables = (new NodeFinder)->findInstanceOf($this->getFile()->getNewStmts(), Variable::class);
        $names = [...$this->insetReservedVariables, ...array_map(static fn (Variable $variable): mixed => $variable->name, $variables)];
        $name = 'filamentInsetComponent';

        while (in_array($name, $names, true)) {
            $name .= '_';
        }

        $this->insetReservedVariables[] = $name;

        return $name;
    }
}
