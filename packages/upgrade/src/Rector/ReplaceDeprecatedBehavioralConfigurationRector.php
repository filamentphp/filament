<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PHPStan\Type\BooleanType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;

class ReplaceDeprecatedBehavioralConfigurationRector extends AbstractRector
{
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
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable() || count($node->args) > 1) {
            return null;
        }

        $method = strtolower($node->name->toString());
        $factory = $node->var;

        while ($factory instanceof MethodCall || $factory instanceof NullsafeMethodCall) {
            if ((! $factory->name instanceof Identifier) || $factory->isFirstClassCallable()) {
                return null;
            }

            $factory = $factory->var;
        }

        $type = ($factory instanceof StaticCall && $factory->class instanceof Name && $this->isName($factory->name, 'make'))
            ? new ObjectType($this->getName($factory->class))
            : TypeCombinator::removeNull($this->getType($factory));
        $owner = null;

        foreach ([
            BaseFileUpload::class => ['disablepreview'],
            FileUpload::class => ['imagecropaspectratio'],
            Builder::class => ['disableitemcreation', 'disableitemdeletion', 'disableitemmovement', 'inset'],
            Repeater::class => ['disableitemcreation', 'disableitemdeletion', 'disableitemmovement', 'inset'],
            KeyValue::class => ['disableaddingrows', 'disabledeletingrows', 'disableeditingkeys', 'disableeditingvalues'],
            TextInput::class => ['disableautocomplete', 'disableautocapitalize'],
            Textarea::class => ['disableautocomplete'],
            Select::class => ['disableplaceholderselection'],
            DateTimePicker::class => ['withoutdate', 'withoutseconds', 'withouttime', 'icon'],
            Action::class => ['centermodal'],
            CreateAction::class => ['disablecreateanother'],
            AttachAction::class => ['disableattachanother'],
            AssociateAction::class => ['disableassociateanother'],
        ] as $class => $methods) {
            if (in_array($method, $methods, true) && (new ObjectType($class))->isSuperTypeOf($type)->yes()) {
                $owner = (new ObjectType($class))->getClassReflection();

                break;
            }
        }

        if (! $owner) {
            return null;
        }

        $replacement = match ($method) {
            'imagecropaspectratio' => 'imageAspectRatio',
            'icon' => 'suffixIcon',
            'centermodal' => 'modalAlignment',
            'inset' => 'tap',
            default => self::NEGATIONS[$method],
        };

        foreach ($type->getObjectClassReflections() as $reflection) {
            foreach ([$method, $replacement] as $name) {
                if ($reflection->hasNativeMethod($name) && ((! $owner->hasNativeMethod($name)) || ($reflection->getNativeMethod($name)->getDeclaringClass()->getName() !== $owner->getNativeMethod($name)->getDeclaringClass()->getName()))) {
                    return null;
                }
            }
        }

        $argument = $node->getArgs()[0] ?? null;
        $parameter = match ($method) {
            'imagecropaspectratio' => 'ratio',
            'icon' => 'icon',
            default => 'condition',
        };

        if ($argument && ($argument->unpack || $argument->byRef || ($argument->name && $argument->name->toString() !== $parameter))) {
            return null;
        }

        $value = $argument->value ?? new ConstFetch(new Name($method === 'icon' ? 'null' : 'true'));
        $literal = $value instanceof ConstFetch ? strtolower($value->name->toString()) : null;
        $componentName = 'configurationComponent';

        while (str_contains($this->getFile()->getFileContent(), '$' . $componentName)) {
            $componentName .= 'Value';
        }

        $component = new Variable($componentName);

        if (($method === 'inset') || (($method === 'icon') && ($literal === 'false')) || (($method === 'centermodal') && in_array($literal, ['false', 'null'], true))) {
            if (($method === 'inset') && (! $value instanceof ConstFetch) && (! $value instanceof Variable) && (! $value instanceof Node\Expr\Closure) && (! $value instanceof ArrowFunction)) {
                return null;
            }

            // Keep the nullsafe boundary for subsequent fluent calls.
            if ($node instanceof NullsafeMethodCall) {
                $node->name = new Identifier('tap');
                $node->args = [new Arg(new ArrowFunction(['static' => true, 'expr' => new ConstFetch(new Name('null'))]))];

                return $node;
            }

            return $node->var;
        }

        if ($method === 'imagecropaspectratio') {
            if (! $argument) {
                return null;
            }

            if (($value instanceof String_) || ($literal === 'null')) {
                $node->name = new Identifier('imageAspectRatio');
                $node->args = [new Arg($value)];

                return new MethodCall($node, 'automaticallyCropImagesToAspectRatio', [new Arg(new ConstFetch(new Name(($value instanceof String_ && trim($value->value) !== '') ? 'true' : 'false')))]);
            }

            $node->name = new Identifier('imageAspectRatio');
            $node->args = [new Arg($value)];

            return new MethodCall($node, 'automaticallyCropImagesToAspectRatio', [new Arg(new ArrowFunction([
                'params' => [new Param($component, type: new FullyQualified(FileUpload::class))],
                'expr' => new FuncCall(new FullyQualified('filled'), [new Arg(new MethodCall($component, 'getImageAspectRatio'))]),
            ]))]);
        }

        if ($method === 'icon') {
            // A runtime false means “do not change the existing icon”.
            if (($literal !== 'null') && (! $this->getNativeType($value)->isString()->yes()) && (! (new ObjectType('BackedEnum'))->isSuperTypeOf($this->getNativeType($value))->yes()) && (! (new ObjectType(Closure::class))->isSuperTypeOf($this->getNativeType($value))->yes())) {
                return null;
            }

            $node->name = new Identifier('suffixIcon');
            $node->args = [new Arg($literal === 'null' ? new ClassConstFetch(new FullyQualified('Filament\\Support\\Icons\\Heroicon'), 'Calendar') : $value), new Arg(new ConstFetch(new Name('true')), name: new Identifier('isInline'))];

            return $node;
        }

        if ($method === 'centermodal') {
            $alignment = new ClassConstFetch(new FullyQualified('Filament\\Support\\Enums\\Alignment'), 'Center');

            if ($literal !== 'true') {
                if ((! $value instanceof Variable) && (! $value instanceof Node\Expr\Closure) && (! $value instanceof ArrowFunction)) {
                    return null;
                }

                // The legacy condition was evaluated immediately, not when
                // the modal opens. Preserve the existing alignment when false.
                $node->name = new Identifier('tap');
                $callback = new ArrowFunction([
                    'params' => [new Param($component)],
                    'expr' => new Ternary(new MethodCall($component, 'evaluate', [new Arg($value)]), new MethodCall($component, 'modalAlignment', [new Arg($alignment)]), $component),
                ]);

                if ($value instanceof Node\Expr\Closure) {
                    $callback = new Node\Expr\Closure([
                        'params' => $callback->params,
                        'uses' => $value->uses,
                        'stmts' => [new Return_($callback->expr)],
                    ]);
                }

                $node->args = [new Arg($callback)];

                return $node;
            }

            $node->name = new Identifier('modalAlignment');
            $node->args = [new Arg($alignment)];

            return $node;
        }

        $isAutocomplete = in_array($method, ['disableautocomplete', 'disableautocapitalize'], true);

        if (in_array($literal, ['true', 'false'], true)) {
            $condition = new ConstFetch(new Name(($literal === 'true') ? 'false' : ($isAutocomplete ? 'null' : 'true')));
        } elseif ((new BooleanType)->isSuperTypeOf($this->getNativeType($value))->yes()) {
            $condition = $isAutocomplete ? new Ternary($value, new ConstFetch(new Name('false')), new ConstFetch(new Name('null'))) : new BooleanNot($value);
        } elseif (($value instanceof ArrowFunction) || ($value instanceof Node\Expr\Closure)) {
            $condition = clone $value;
            $condition->returnType = null;

            if ($condition instanceof ArrowFunction) {
                $condition->expr = $isAutocomplete ? new Ternary($condition->expr, new ConstFetch(new Name('false')), new ConstFetch(new Name('null'))) : new BooleanNot($condition->expr);
            } else {
                $this->traverseNodesWithCallable($condition->stmts, static function (Node $statement) use ($isAutocomplete): ?int {
                    if (($statement instanceof Node\FunctionLike) || ($statement instanceof Node\Stmt\ClassLike)) {
                        return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
                    }

                    if (($statement instanceof Return_) && $statement->expr) {
                        $statement->expr = $isAutocomplete ? new Ternary($statement->expr, new ConstFetch(new Name('false')), new ConstFetch(new Name('null'))) : new BooleanNot($statement->expr);
                    }

                    return null;
                });
            }
        } elseif (($value instanceof Variable) && TypeCombinator::union(new BooleanType, new ObjectType(Closure::class))->isSuperTypeOf($this->getNativeType($value))->yes()) {
            $evaluation = new MethodCall($component, 'evaluate', [new Arg($value)]);
            $condition = new ArrowFunction([
                'params' => [new Param($component, type: new FullyQualified($owner->getName()))],
                'expr' => $isAutocomplete ? new Ternary($evaluation, new ConstFetch(new Name('false')), new ConstFetch(new Name('null'))) : new BooleanNot($evaluation),
            ]);
        } else {
            return null;
        }

        $node->name = new Identifier($replacement);
        $node->args = [new Arg($condition)];

        return $node;
    }
}
