<?php

namespace Filament\Support\Rector;

use Filament\Forms\Components\Concerns\CanBeValidated;
use Filament\Schemas\Components\Concerns\HasState;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AlphabetizeFluentMethodsRector extends AbstractRector
{
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

        // Check if the outermost method is non-fluent or terminal
        $rootType = $this->nodeTypeResolver->getType($rootNode);
        $nonFluentTail = null;
        $terminalMethods = [];
        $chainToProcess = $node;

        // Collect non-fluent and terminal methods from the end
        $current = $node;
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
            } elseif ($this->isTerminalActionMethod($methodName)) {
                // Terminal action method, save it
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

        // Get the class name for dynamic trait checking
        $className = $this->getClassName($rootNode);

        // Split the chain at clone() boundaries and sort each segment independently
        $sortedCalls = $this->sortMethodCallsWithCloneBoundaries($methodCalls, $className);

        // Check if the order changed
        if ($sortedCalls === $methodCalls) {
            return null;
        }

        // Rebuild the chain with sorted methods
        $rebuiltChain = $this->rebuildChain($rootNode, $sortedCalls);

        // Sort and reattach terminal methods in the correct order
        $sortedTerminalMethods = $this->sortTerminalMethods($terminalMethods);
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
     * Sort terminal methods according to their lifecycle order
     *
     * @param  array<array{name: string, call: MethodCall}>  $terminalMethods
     * @return array<array{name: string, call: MethodCall}>
     */
    private function sortTerminalMethods(array $terminalMethods): array
    {
        usort($terminalMethods, function ($a, $b) {
            return $this->getTerminalMethodPriority($a['name']) <=> $this->getTerminalMethodPriority($b['name']);
        });

        return $terminalMethods;
    }

    /**
     * Get the priority order for terminal methods (lower = earlier in chain)
     */
    private function getTerminalMethodPriority(string $methodName): int
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

        // Notification terminal methods (send, broadcast, etc.) come last
        $notificationTerminals = ['send', 'sendToDatabase', 'broadcast', 'dispatch', 'sendToQueue'];
        if (in_array($methodName, $notificationTerminals, true)) {
            return 100;
        }

        // Default priority for unknown terminal methods
        return 999;
    }

    /**
     * Check if a method is a terminal action method that should stay at the end
     */
    private function isTerminalActionMethod(?string $methodName): bool
    {
        if ($methodName === null) {
            return false;
        }

        // Methods that execute actions and should remain at the end
        $terminalMethods = [
            'send',
            'sendToDatabase',
            'broadcast',
            'dispatch',
            'sendToQueue',
        ];

        // Action lifecycle methods
        $lifecycleMethods = [
            'before',
            'action',
            'using',
            'after',
        ];

        if (in_array($methodName, $terminalMethods, true)) {
            return true;
        }

        if (in_array($methodName, $lifecycleMethods, true)) {
            return true;
        }

        // success* and failure* methods for Actions
        if (str_starts_with($methodName, 'success') || str_starts_with($methodName, 'failure')) {
            return true;
        }

        return false;
    }

    private function getRootNode(MethodCall $node): Node
    {
        $current = $node;

        while ($current->var instanceof MethodCall) {
            $current = $current->var;
        }

        return $current->var;
    }

    private function getClassName(Node $node): ?string
    {
        if ($node instanceof StaticCall) {
            $callerType = $this->nodeTypeResolver->getType($node->class);

            if ($callerType instanceof ObjectType) {
                return $callerType->getClassName();
            }
        }

        return null;
    }

    private function isFilamentClass(Node $node): bool
    {
        // For StaticCall, check the class name
        if ($node instanceof StaticCall) {
            $callerType = $this->nodeTypeResolver->getType($node->class);

            if ($callerType instanceof ObjectType) {
                $className = $callerType->getClassName();

                // Exclude classes that have order-dependent builder methods
                if ($this->isExcludedClass($className)) {
                    return false;
                }

                return str_starts_with($className, 'Filament\\');
            }
        }

        // For other expressions, check their type
        $nodeType = $this->nodeTypeResolver->getType($node);

        if ($nodeType instanceof ObjectType) {
            $className = $nodeType->getClassName();

            // Exclude classes that have order-dependent builder methods
            if ($this->isExcludedClass($className)) {
                return false;
            }

            return str_starts_with($className, 'Filament\\');
        }

        return false;
    }

    /**
     * Check if a class should be excluded from reordering
     */
    private function isExcludedClass(string $className): bool
    {
        // Classes with order-dependent builder methods
        $excludedClasses = [
            'Filament\\Schemas\\Schema',
        ];

        foreach ($excludedClasses as $excludedClass) {
            if ($className === $excludedClass || is_subclass_of($className, $excludedClass)) {
                return true;
            }
        }

        return false;
    }

    private function isChainStartingWithMake(Node $node): bool
    {
        if ($node instanceof StaticCall) {
            return $this->isName($node->name, 'make');
        }

        return false;
    }

    private function isOutermostMethodCall(MethodCall $node): bool
    {
        // Check if this method call is not part of a larger method call chain
        $parent = $node->getAttribute('parent');

        return ! ($parent instanceof MethodCall && $parent->var === $node);
    }

    /**
     * @return array<array{name: string, call: MethodCall}>
     */
    private function collectMethodCalls(MethodCall $node): array
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

    /**
     * Check if a method is a fluent builder method by checking if it returns the same type
     */
    private function isFluentBuilderMethod(MethodCall $methodCall, $rootType): bool
    {
        // Get the return type of this method call
        $returnType = $this->nodeTypeResolver->getType($methodCall);

        // Check if the return type matches the root type (fluent interface)
        // This means the method returns $this or the same class type
        if ($rootType instanceof ObjectType && $returnType instanceof ObjectType) {
            return $rootType->getClassName() === $returnType->getClassName();
        }

        // If we can't determine the types, fall back to name-based heuristics
        // Exclude methods that are clearly not fluent based on common naming patterns
        $methodName = $this->getName($methodCall->name);
        if ($methodName === null) {
            return false;
        }

        // Exclude getter/query methods that typically return different types
        $nonFluentPrefixes = ['get', 'is', 'has', 'can', 'should', 'to', 'find', 'all', 'first', 'count', 'exists'];
        foreach ($nonFluentPrefixes as $prefix) {
            if (str_starts_with($methodName, $prefix)) {
                return false;
            }
        }

        // Assume it's fluent if it doesn't match non-fluent patterns
        return true;
    }

    /**
     * Get the trait that defines a method by checking if it's defined in CanBeValidated or HasState
     */
    private function getMethodDefiningTrait(string $className, string $methodName): ?string
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
     * Get all traits used by a class, including traits used by parent classes and traits
     */
    private function getAllTraits(\ReflectionClass $class): array
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
     * Split chain at clone*() boundaries and sort each segment independently
     *
     * @param  array<array{name: string, call: MethodCall}>  $methodCalls
     * @return array<array{name: string, call: MethodCall}>
     */
    private function sortMethodCallsWithCloneBoundaries(array $methodCalls, ?string $className): array
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
    private function sortMethodCalls(array $methodCalls, ?string $className): array
    {
        // Check if this is a Notification class
        $isNotification = $className && str_contains($className, 'Notification');

        // Categorize methods
        $titleMethods = [];
        $bodyMethods = [];
        $labelMethods = [];
        $regularMethods = [];
        $validationMethods = [];
        $stateMethods = [];

        foreach ($methodCalls as $callData) {
            $methodName = $callData['name'];

            if ($isNotification && $methodName === 'title') {
                $titleMethods[] = $callData;
            } elseif ($isNotification && $methodName === 'body') {
                $bodyMethods[] = $callData;
            } elseif ($methodName === 'label') {
                $labelMethods[] = $callData;
            } elseif ($className && $this->getMethodDefiningTrait($className, $methodName) === CanBeValidated::class) {
                $validationMethods[] = $callData;
            } elseif ($className && $this->getMethodDefiningTrait($className, $methodName) === HasState::class) {
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

        // For Notifications: title, body, regular, validation, state
        // For others: label, regular, validation, state
        if ($isNotification) {
            return array_merge($titleMethods, $bodyMethods, $regularMethods, $validationMethods, $stateMethods);
        }

        return array_merge($labelMethods, $regularMethods, $validationMethods, $stateMethods);
    }

    /**
     * @param  array<array{name: string, call: MethodCall}>  $sortedCalls
     */
    private function rebuildChain(Node $rootNode, array $sortedCalls): MethodCall
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
