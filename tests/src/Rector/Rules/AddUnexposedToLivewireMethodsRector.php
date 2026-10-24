<?php

namespace Filament\Tests\Rector\Rules;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Livewire\Component;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function Filament\Support\is_path_within_directory;

class AddUnexposedToLivewireMethodsRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var array<string> */
    protected array $paths = [];

    /** @var array<string> */
    protected array $componentPaths = [];

    /** @var ?array<string, true> */
    protected ?array $livewireTraits = null;

    public function __construct(
        protected ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Mark public Livewire component and component trait methods as unexposed unless explicitly exposed.', []);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    /** @param Class_ | Trait_ $node */
    public function refactor(Node $node): ?Node
    {
        if (! array_filter($this->paths, fn (string $path): bool => is_path_within_directory($this->getFile()->getFilePath(), $path))) {
            return null;
        }

        $class = ScopeFetcher::fetch($node)->getClassReflection();

        if (! $class) {
            return null;
        }

        if ($node instanceof Trait_) {
            if (! isset($this->getLivewireTraits()[$class->getName()])) {
                return null;
            }
        } elseif (! $class->isSubclassOf(Component::class)) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->getMethods() as $method) {
            if (! $method->isPublic()) {
                continue;
            }

            foreach ($method->attrGroups as $attributeGroup) {
                foreach ($attributeGroup->attrs as $attribute) {
                    $attributeClass = $this->getName($attribute->name);

                    if ($this->getMethodAccessAttributeClass($attributeClass) !== null) {
                        continue 3;
                    }
                }
            }

            $method->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified(Unexposed::class))]);
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    protected function getMethodAccessAttributeClass(string $attributeClass): ?string
    {
        if (in_array($attributeClass, [Exposed::class, Unexposed::class], strict: true)) {
            return $attributeClass;
        }

        if (! $this->reflectionProvider->hasClass($attributeClass)) {
            return null;
        }

        $attribute = $this->reflectionProvider->getClass($attributeClass);

        if ($attribute->isSubclassOf(Unexposed::class)) {
            return Unexposed::class;
        }

        return $attribute->isSubclassOf(Exposed::class) ? Exposed::class : null;
    }

    /** @return array<string, true> */
    protected function getLivewireTraits(): array
    {
        if ($this->livewireTraits !== null) {
            return $this->livewireTraits;
        }

        $this->livewireTraits = [];

        foreach ($this->componentPaths as $path) {
            foreach (ClassMapGenerator::createMap($path) as $className => $filePath) {
                if (! $this->reflectionProvider->hasClass($className)) {
                    continue;
                }

                $class = $this->reflectionProvider->getClass($className);

                if (! $class->isSubclassOf(Component::class)) {
                    continue;
                }

                foreach ($class->getAncestors() as $ancestor) {
                    foreach ($ancestor->getTraits(true) as $trait) {
                        $this->livewireTraits[$trait->getName()] = true;
                    }
                }
            }
        }

        return $this->livewireTraits;
    }

    /** @param array{paths: array<string>, componentPaths?: array<string>} $configuration */
    public function configure(array $configuration): void
    {
        $this->paths = $configuration['paths'];
        $this->componentPaths = $configuration['componentPaths'] ?? $this->paths;
        $this->livewireTraits = null;
    }
}
