<?php

namespace Filament\Upgrade\Rector;

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
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use ReflectionMethod;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace literal behavioral configuration on built-in component factories', [
            new CodeSample("FileUpload::make('photo')->imageCropAspectRatio('16:9')", "FileUpload::make('photo')->imageAspectRatio('16:9')->automaticallyCropImagesToAspectRatio(true)"),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /** @param MethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if ((! $node->name instanceof Identifier) || $node->isFirstClassCallable()) {
            return null;
        }

        $method = strtolower($node->name->toString());
        $factory = $node->var;
        $precedingMethods = [];

        // Do not infer a base type for custom receivers, unions, parent calls,
        // or chains whose callbacks may return a different component.
        while ($factory instanceof MethodCall) {
            if ((! $factory->name instanceof Identifier) || $factory->isFirstClassCallable() || (! in_array(strtolower($factory->name->toString()), [
                ...array_keys(self::NEGATIONS), ...array_map('strtolower', array_values(self::NEGATIONS)),
                'imagecropaspectratio', 'imageaspectratio', 'automaticallycropimagestoaspectratio',
                'centermodal', 'modalalignment', 'icon', 'suffixicon', 'inset', 'label',
            ], true))) {
                return null;
            }

            $precedingMethods[] = strtolower($factory->name->toString());
            $factory = $factory->var;
        }

        if ((! $factory instanceof StaticCall) || (! $factory->class instanceof Name) || (! $this->isName($factory->name, 'make')) || $factory->isFirstClassCallable()) {
            return null;
        }

        $class = $this->getName($factory->class);
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
            if ((! $argument) || ((! $value instanceof String_) && ($literal !== 'null'))) {
                return null;
            }

            $ratio = ($value instanceof String_) ? $value->value : null;

            return new MethodCall(new MethodCall($node->var, 'imageAspectRatio', [new Arg($value)]), 'automaticallyCropImagesToAspectRatio', [new Arg(new ConstFetch(new Name((trim($ratio ?? '') !== '') ? 'true' : 'false')))]);
        }

        if ($method === 'icon') {
            if ($literal === 'false') {
                return $node->var;
            }

            if ((! $value instanceof String_) && ($literal !== 'null')) {
                return null;
            }

            return new MethodCall($node->var, 'suffixIcon', [
                new Arg(($literal === 'null') ? new ClassConstFetch(new FullyQualified('Filament\\Support\\Icons\\Heroicon'), 'Calendar') : $value),
                new Arg(new ConstFetch(new Name('true')), name: new Identifier('isInline')),
            ]);
        }

        if ((! in_array($literal, ['true', 'false'], true)) && (($method !== 'centermodal') || ($literal !== 'null'))) {
            return null;
        }

        if ($method === 'inset') {
            return $node->var;
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
}
