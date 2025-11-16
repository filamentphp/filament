<?php

namespace Filament\Support\Rector;

use Filament\Actions\Action;
use Filament\Forms\Components\Concerns\CanBeValidated;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Concerns\HasState;
use Filament\Schemas\Schema;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\Type;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AlphabetizeFluentMethodsRector extends AbstractRector
{
    /**
     * @var array<string, ?string>
     */
    private array $methodTraitCache = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Alphabetize methods in fluent builder chains for Filament classes with specific ordering rules',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
TextInput::make('name')
    ->default('test')
    ->sortable()
    ->searchable()
    ->required()
    ->label('Name');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
TextInput::make('name')
    ->label('Name')
    ->searchable()
    ->sortable()
    ->required()
    ->default('test');
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        // Get the root of the chain
        $rootNode = $this->getRootNode($node);

        // Check if this is a Filament class
        if (! $this->isFilamentClass($rootNode)) {
            return null;
        }

        // Only process chains that start with a ::make() static call
        if (! $this->isChainStartingWithMake($rootNode)) {
            return null;
        }

        // Only process the outermost method call in the chain
        if (! $this->isOutermostMethodCall($node)) {
            return null;
        }

        // Get the class name early for terminal method detection
        $className = $this->getClassName($rootNode);

        // Check if the outermost method is non-fluent or terminal
        $rootType = $this->nodeTypeResolver->getType($rootNode);
        $nonFluentTail = null;
        $terminalMethods = [];
        $chainToProcess = $node;

        // Collect non-fluent and terminal methods from the end
        $current = $node;
        // @phpstan-ignore-next-line instanceof.alwaysTrue - First iteration is always true, but $current is reassigned in loop
        while ($current instanceof MethodCall) {
            $methodName = $this->getName($current->name);

            if (! $this->isFluentBuilderMethod($current, $rootType)) {
                // Non-fluent method, save it
                $nonFluentTail = $current;
                $chainToProcess = $current->var;

                if (! $chainToProcess instanceof MethodCall) {
                    return null;
                }

                $current = $chainToProcess;
            } elseif ($this->isTerminalMethod($methodName, $className)) {
                // Terminal method, save it
                array_unshift($terminalMethods, ['name' => $methodName, 'call' => $current]);
                $chainToProcess = $current->var;

                if (! $chainToProcess instanceof MethodCall) {
                    // If only terminal methods, nothing to sort
                    return null;
                }

                $current = $chainToProcess;
            } else {
                // Regular fluent method, stop collecting
                break;
            }
        }

        // Collect all method calls in the chain (excluding the make() call itself and terminal methods)
        $methodCalls = $this->collectMethodCalls($chainToProcess);

        // If there are less than 2 methods, no need to sort
        if (count($methodCalls) < 2) {
            return null;
        }

        // Split the chain at clone() boundaries and sort each segment independently
        $sortedCalls = $this->sortMethodCallsWithCloneBoundaries($methodCalls, $className);

        // Check if the order changed
        if ($sortedCalls === $methodCalls) {
            return null;
        }

        // Rebuild the chain with sorted methods
        $rebuiltChain = $this->rebuildChain($rootNode, $sortedCalls);

        // Sort and reattach terminal methods in the correct order
        $sortedTerminalMethods = $this->sortTerminalMethods($terminalMethods, $className);
        foreach ($sortedTerminalMethods as $terminalData) {
            $newTerminal = clone $terminalData['call'];
            $newTerminal->var = $rebuiltChain;
            $rebuiltChain = $newTerminal;
        }

        // If there was a non-fluent tail, reattach it
        if ($nonFluentTail !== null) {
            $newTail = clone $nonFluentTail;
            $newTail->var = $rebuiltChain;

            return $newTail;
        }

        return $rebuiltChain;
    }

    /**
     * Check if a method is a terminal method that should stay at the end
     */
    protected function isTerminalMethod(?string $methodName, ?string $className): bool
    {
        if ($methodName === null || $className === null) {
            return false;
        }

        if ($this->isAction($className)) {
            return $this->isActionTerminalMethod($methodName);
        }

        if ($this->isNotification($className)) {
            return $this->isNotificationTerminalMethod($methodName);
        }

        return false;
    }

    /**
     * Check if a method is a terminal method for Actions
     */
    protected function isActionTerminalMethod(string $methodName): bool
    {
        // Action lifecycle methods
        $lifecycleMethods = [
            'before',
            'action',
            'using',
            'after',
        ];

        if (in_array($methodName, $lifecycleMethods, true)) {
            return true;
        }

        // success* and failure* methods for Actions
        if (str_starts_with($methodName, 'success') || str_starts_with($methodName, 'failure')) {
            return true;
        }

        return false;
    }

    /**
     * Check if a method is a terminal method for Notifications
     */
    protected function isNotificationTerminalMethod(string $methodName): bool
    {
        // Methods that execute/send notifications and should remain at the end
        $terminalMethods = [
            'send',
            'sendToDatabase',
            'broadcast',
            'dispatch',
            'sendToQueue',
        ];

        return in_array($methodName, $terminalMethods, true);
    }

    /**
     * Sort terminal methods according to class-specific ordering
     *
     * @param  array<array{name: string, call: MethodCall}>  $terminalMethods
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortTerminalMethods(array $terminalMethods, ?string $className): array
    {
        if ($className === null) {
            return $terminalMethods;
        }

        if ($this->isAction($className)) {
            return $this->sortActionTerminalMethods($terminalMethods);
        }

        if ($this->isNotification($className)) {
            return $this->sortNotificationTerminalMethods($terminalMethods);
        }

        return $terminalMethods;
    }

    /**
     * Sort terminal methods for Actions according to their lifecycle order
     *
     * @param  array<array{name: string, call: MethodCall}>  $terminalMethods
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortActionTerminalMethods(array $terminalMethods): array
    {
        usort($terminalMethods, function ($a, $b) {
            $priorityA = $this->getActionTerminalMethodPriority($a['name']);
            $priorityB = $this->getActionTerminalMethodPriority($b['name']);

            return $priorityA <=> $priorityB;
        });

        return $terminalMethods;
    }

    /**
     * Get the priority order for Action terminal methods (lower = earlier in chain)
     */
    protected function getActionTerminalMethodPriority(string $methodName): int
    {
        // Action lifecycle methods (in order)
        if ($methodName === 'before') {
            return 10;
        }
        if ($methodName === 'action') {
            return 20;
        }
        if ($methodName === 'using') {
            return 30;
        }
        if ($methodName === 'after') {
            return 40;
        }

        // Success methods come after lifecycle
        if (str_starts_with($methodName, 'success')) {
            return 50;
        }

        // Failure methods come after success methods
        if (str_starts_with($methodName, 'failure')) {
            return 60;
        }

        // Default priority for unknown terminal methods
        return 999;
    }

    /**
     * Sort terminal methods for Notifications
     *
     * @param  array<array{name: string, call: MethodCall}>  $terminalMethods
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortNotificationTerminalMethods(array $terminalMethods): array
    {
        usort($terminalMethods, function ($a, $b) {
            $priorityA = $this->getNotificationTerminalMethodPriority($a['name']);
            $priorityB = $this->getNotificationTerminalMethodPriority($b['name']);

            return $priorityA <=> $priorityB;
        });

        return $terminalMethods;
    }

    /**
     * Get the priority order for Notification terminal methods (lower = earlier in chain)
     */
    protected function getNotificationTerminalMethodPriority(string $methodName): int
    {
        // Notification terminal methods ordering
        $notificationTerminals = [
            'send' => 10,
            'sendToDatabase' => 20,
            'broadcast' => 30,
        ];

        return $notificationTerminals[$methodName] ?? 999;
    }

    protected function getRootNode(MethodCall $node): Expr
    {
        $current = $node;

        while ($current->var instanceof MethodCall) {
            $current = $current->var;
        }

        return $current->var;
    }

    protected function getClassName(Node $node): ?string
    {
        if ($node instanceof StaticCall) {
            $callerType = $this->nodeTypeResolver->getType($node->class);

            if ($callerType->isObject()->yes()) {
                $classNames = $callerType->getObjectClassNames();

                return $classNames[0] ?? null;
            }
        }

        return null;
    }

    protected function isFilamentClass(Node $node): bool
    {
        // For StaticCall, check the class name
        if ($node instanceof StaticCall) {
            $callerType = $this->nodeTypeResolver->getType($node->class);

            if ($callerType->isObject()->yes()) {
                $classNames = $callerType->getObjectClassNames();
                foreach ($classNames as $className) {
                    // Exclude classes that have order-dependent builder methods
                    if ($this->isExcludedClass($className)) {
                        return false;
                    }

                    if (str_starts_with($className, 'Filament\\')) {
                        return true;
                    }
                }
            }
        }

        // For other expressions, check their type
        $nodeType = $this->nodeTypeResolver->getType($node);

        if ($nodeType->isObject()->yes()) {
            $classNames = $nodeType->getObjectClassNames();
            foreach ($classNames as $className) {
                // Exclude classes that have order-dependent builder methods
                if ($this->isExcludedClass($className)) {
                    return false;
                }

                if (str_starts_with($className, 'Filament\\')) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function isExcludedClass(string $className): bool
    {
        // Classes with order-dependent builder methods
        $excludedClasses = [
            Schema::class,
        ];

        foreach ($excludedClasses as $excludedClass) {
            if ($className === $excludedClass || is_subclass_of($className, $excludedClass)) {
                return true;
            }
        }

        return false;
    }

    protected function isChainStartingWithMake(Node $node): bool
    {
        if ($node instanceof StaticCall) {
            return $this->isName($node->name, 'make');
        }

        return false;
    }

    protected function isOutermostMethodCall(MethodCall $node): bool
    {
        // Check if this method call is not part of a larger method call chain
        $parent = $node->getAttribute('parent');

        return ! ($parent instanceof MethodCall && $parent->var === $node);
    }

    /**
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function collectMethodCalls(MethodCall $node): array
    {
        $calls = [];
        $current = $node;
        $rootType = $this->nodeTypeResolver->getType($this->getRootNode($node));

        while ($current instanceof MethodCall) {
            $methodName = $this->getName($current->name);

            if ($methodName !== null && $this->isFluentBuilderMethod($current, $rootType)) {
                // Add to the beginning of the array to maintain original order
                array_unshift($calls, [
                    'name' => $methodName,
                    'call' => $current,
                ]);
            }

            $current = $current->var;
        }

        return $calls;
    }

    protected function isFluentBuilderMethod(MethodCall $methodCall, Type $rootType): bool
    {
        // Get the return type of this method call
        $returnType = $this->nodeTypeResolver->getType($methodCall);

        // Check if the return type matches the root type (fluent interface)
        // This means the method returns $this or the same class type
        if ($rootType->isObject()->yes() && $returnType->isObject()->yes()) {
            $rootClassNames = $rootType->getObjectClassNames();
            $returnClassNames = $returnType->getObjectClassNames();

            // Check if any of the class names match
            return ! empty(array_intersect($rootClassNames, $returnClassNames));
        }

        return false;
    }

    protected function getMethodDefiningTrait(string $className, string $methodName): ?string
    {
        $cacheKey = $className . '::' . $methodName;

        if (isset($this->methodTraitCache[$cacheKey])) {
            return $this->methodTraitCache[$cacheKey];
        }

        // Try to get the class reflection
        if (! class_exists($className) && ! trait_exists($className)) {
            $this->methodTraitCache[$cacheKey] = null;

            return null;
        }

        try {
            $reflection = new \ReflectionClass($className);

            // Check if the method exists in the class or its traits
            if (! $reflection->hasMethod($methodName)) {
                $this->methodTraitCache[$cacheKey] = null;

                return null;
            }

            $method = $reflection->getMethod($methodName);
            $declaringClass = $method->getDeclaringClass();

            // Check if it's from the validation trait
            if ($declaringClass->getName() === CanBeValidated::class) {
                $this->methodTraitCache[$cacheKey] = CanBeValidated::class;

                return CanBeValidated::class;
            }

            // Check if it's from the state trait
            if ($declaringClass->getName() === HasState::class) {
                $this->methodTraitCache[$cacheKey] = HasState::class;

                return HasState::class;
            }

            // Check if the declaring class uses these traits
            $traits = $this->getAllTraits($declaringClass);

            if (in_array(CanBeValidated::class, $traits, true)) {
                // Check if method is defined in the validation trait
                if (trait_exists(CanBeValidated::class)) {
                    $traitReflection = new \ReflectionClass(CanBeValidated::class);
                    if ($traitReflection->hasMethod($methodName)) {
                        $this->methodTraitCache[$cacheKey] = CanBeValidated::class;

                        return CanBeValidated::class;
                    }
                }
            }

            if (in_array(HasState::class, $traits, true)) {
                // Check if method is defined in the state trait
                if (trait_exists(HasState::class)) {
                    $traitReflection = new \ReflectionClass(HasState::class);
                    if ($traitReflection->hasMethod($methodName)) {
                        $this->methodTraitCache[$cacheKey] = HasState::class;

                        return HasState::class;
                    }
                }
            }
        } catch (\ReflectionException $e) {
            // Class doesn't exist or can't be reflected
        }

        $this->methodTraitCache[$cacheKey] = null;

        return null;
    }

    /**
     * @return array<string>
     */
    protected function getAllTraits(\ReflectionClass $class): array
    {
        $traits = [];

        do {
            $traits = array_merge($traits, $class->getTraitNames());
        } while ($class = $class->getParentClass());

        // Also get traits of traits
        $traitTraits = [];
        foreach ($traits as $trait) {
            if (trait_exists($trait)) {
                $traitReflection = new \ReflectionClass($trait);
                $traitTraits = array_merge($traitTraits, $this->getAllTraits($traitReflection));
            }
        }

        return array_unique(array_merge($traits, $traitTraits));
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortMethodCallsWithCloneBoundaries(array $methodCalls, ?string $className): array
    {
        // Split the chain into segments at clone*() boundaries
        $segments = [];
        $currentSegment = [];

        foreach ($methodCalls as $callData) {
            if (str_starts_with($callData['name'], 'clone')) {
                // Sort the current segment before adding clone
                if (! empty($currentSegment)) {
                    $segments[] = $this->sortMethodCalls($currentSegment, $className);
                }
                // Add clone method as its own segment (don't reorder it)
                $segments[] = [$callData];
                $currentSegment = [];
            } else {
                $currentSegment[] = $callData;
            }
        }

        // Sort the last segment
        if (! empty($currentSegment)) {
            $segments[] = $this->sortMethodCalls($currentSegment, $className);
        }

        // Flatten the segments back into a single array
        return array_merge(...$segments);
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortMethodCalls(array $methodCalls, ?string $className): array
    {
        if (! $className) {
            return $this->sortDefaultMethods($methodCalls);
        }

        // Determine which sorting behavior to use based on class type
        if ($this->isAction($className)) {
            return $this->sortActionMethods($methodCalls, $className);
        }

        if ($this->isNotification($className)) {
            return $this->sortNotificationMethods($methodCalls, $className);
        }

        // Check if this is a Component (forms/schemas/tables components)
        if ($this->isSchemaComponent($className)) {
            return $this->sortSchemaComponentMethods($methodCalls, $className);
        }

        return $this->sortDefaultMethods($methodCalls);
    }

    protected function isAction(string $className): bool
    {
        if (! class_exists($className)) {
            return false;
        }

        return is_a($className, Action::class, true);
    }

    protected function isNotification(string $className): bool
    {
        if (! class_exists($className)) {
            return false;
        }

        return is_a($className, Notification::class, true);
    }

    protected function isSchemaComponent(string $className): bool
    {
        if (! class_exists($className)) {
            return false;
        }

        return is_a($className, SchemaComponent::class, true);
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortActionMethods(array $methodCalls, string $className): array
    {
        $labelMethods = [];
        $regularMethods = [];

        foreach ($methodCalls as $callData) {
            $methodName = $callData['name'];

            if ($methodName === 'label') {
                $labelMethods[] = $callData;
            } else {
                $regularMethods[] = $callData;
            }
        }

        // Sort regular methods alphabetically
        usort($regularMethods, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return array_merge($labelMethods, $regularMethods);
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortNotificationMethods(array $methodCalls, string $className): array
    {
        $titleMethods = [];
        $bodyMethods = [];
        $regularMethods = [];

        foreach ($methodCalls as $callData) {
            $methodName = $callData['name'];

            if ($methodName === 'title') {
                $titleMethods[] = $callData;
            } elseif ($methodName === 'body') {
                $bodyMethods[] = $callData;
            } else {
                $regularMethods[] = $callData;
            }
        }

        // Sort regular methods alphabetically
        usort($regularMethods, fn ($a, $b) => strcmp($a['name'], $b['name']));

        // Order: title, body, then alphabetically sorted regular methods
        return array_merge($titleMethods, $bodyMethods, $regularMethods);
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortSchemaComponentMethods(array $methodCalls, string $className): array
    {
        $labelMethods = [];
        $regularMethods = [];
        $validationMethods = [];
        $stateMethods = [];

        foreach ($methodCalls as $callData) {
            $methodName = $callData['name'];

            if ($methodName === 'label') {
                $labelMethods[] = $callData;
            } elseif ($this->getMethodDefiningTrait($className, $methodName) === CanBeValidated::class) {
                $validationMethods[] = $callData;
            } elseif ($this->getMethodDefiningTrait($className, $methodName) === HasState::class) {
                $stateMethods[] = $callData;
            } else {
                $regularMethods[] = $callData;
            }
        }

        // Sort regular methods alphabetically
        usort($regularMethods, fn ($a, $b) => strcmp($a['name'], $b['name']));

        // Sort validation methods with 'required' first, then alphabetically
        usort($validationMethods, function ($a, $b) {
            if ($a['name'] === 'required') {
                return -1;
            }
            if ($b['name'] === 'required') {
                return 1;
            }

            return strcmp($a['name'], $b['name']);
        });

        // Sort state methods alphabetically
        usort($stateMethods, fn ($a, $b) => strcmp($a['name'], $b['name']));

        // Order: label, regular, validation, state
        return array_merge($labelMethods, $regularMethods, $validationMethods, $stateMethods);
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    protected function sortDefaultMethods(array $methodCalls): array
    {
        $sortedMethods = $methodCalls;

        // Sort all methods alphabetically
        usort($sortedMethods, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $sortedMethods;
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $sortedCalls
     */
    protected function rebuildChain(Expr $rootNode, array $sortedCalls): MethodCall
    {
        $chain = null;

        foreach ($sortedCalls as $callData) {
            $originalCall = $callData['call'];

            if ($chain === null) {
                // First method in chain - clone the original to preserve attributes
                $newCall = clone $originalCall;
                $newCall->var = $rootNode;
                $chain = $newCall;
            } else {
                // Subsequent methods - clone to preserve attributes
                $newCall = clone $originalCall;
                $newCall->var = $chain;
                $chain = $newCall;
            }

            // Preserve original attributes for formatting
            foreach ($originalCall->getAttributes() as $key => $value) {
                $chain->setAttribute($key, $value);
            }
        }

        return $chain;
    }
}
