<?php

namespace Filament\Tests\Rector\Rules;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Livewire\MethodAccess;
use Livewire\Component;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use RuntimeException;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function Filament\Support\is_path_within_directory;

class AddUnexposedToLivewireMethodsRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var array<string> */
    protected array $paths = [];

    /** @var array<string> */
    protected array $componentPaths = [];

    /** @var ?array<string, array<string, ClassReflection>> */
    protected ?array $livewireTraits = null;

    /** @var array<string, true> */
    protected array $checkedTraitAliases = [];

    public function __construct(
        protected ReflectionProvider $reflectionProvider,
        protected MethodAccess $methodAccess,
        protected AstResolver $astResolver,
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

        // PHPStan silently drops trait aliases whose trait names use different casing.
        $this->assertSupportedTraitAliases($class);

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

            if ($this->methodAccess->getMethodAccess($class->getNativeReflection(), $method->name->toString()) === false) {
                continue;
            }

            if ($node instanceof Trait_) {
                $this->assertTraitMethodDoesNotInheritExposure($class, $method->name->toString());
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

    protected function assertTraitMethodDoesNotInheritExposure(ClassReflection $trait, string $method): void
    {
        foreach ($this->getLivewireTraits()[$trait->getName()] as $component) {
            $this->assertSupportedTraitAliases($component);

            $methods = [strtolower($method) => $method];
            $aliases = [];

            foreach ([$component, ...$component->getParents()] as $ancestor) {
                foreach ([$ancestor, ...$ancestor->getTraits(true)] as $owner) {
                    foreach ($owner->getNativeReflection()->getTraitAliases() as $alias => $original) {
                        $aliases[] = [$alias, strtolower(explode('::', $original, 2)[1])];
                    }
                }
            }

            do {
                $originalMethodCount = count($methods);

                foreach ($aliases as [$alias, $original]) {
                    if (isset($methods[$original])) {
                        $methods[strtolower($alias)] = $alias;
                    }
                }
            } while (count($methods) > $originalMethodCount);

            foreach ($methods as $componentMethod) {
                if ($this->methodAccess->getMethodAccess($component->getNativeReflection(), $componentMethod) === false) {
                    throw new RuntimeException("Trait method [{$trait->getName()}::{$method}] is used by [{$component->getName()}::{$componentMethod}] with an exposed policy. Add an explicit `#[Exposed]` or `#[Unexposed]` attribute to the trait method before rerunning Rector; its consuming component can affect its access policy.");
                }
            }
        }
    }

    protected function assertSupportedTraitAliases(ClassReflection $class): void
    {
        foreach ([$class, ...$class->getParents()] as $ancestor) {
            foreach ([$ancestor, ...$ancestor->getTraits(true)] as $owner) {
                if (isset($this->checkedTraitAliases[$owner->getName()])) {
                    continue;
                }

                $node = $this->astResolver->resolveClassFromClassReflection($owner);

                if (! $node) {
                    throw new RuntimeException("Cannot inspect trait aliases in [{$owner->getName()}]. No method access annotations were added to this declaration.");
                }

                foreach ($node->getTraitUses() as $traitUse) {
                    foreach ($traitUse->adaptations as $adaptation) {
                        if ((! $adaptation instanceof Alias) || ($adaptation->newName === null)) {
                            continue;
                        }

                        foreach ($adaptation->trait ? [$adaptation->trait] : $traitUse->traits as $traitName) {
                            $name = $this->getName($traitName);
                            $canonicalName = $this->reflectionProvider->getClass($name)->getName();

                            if ($name !== $canonicalName) {
                                throw new RuntimeException("Static reflection cannot safely inspect trait alias [{$adaptation->newName}] in [{$owner->getFileName()}:{$adaptation->getStartLine()}]. Use the canonical trait spelling [{$canonicalName}] instead of [{$name}], then rerun Rector. The original PHP is valid; this is a tooling limitation.");
                            }
                        }
                    }
                }

                $this->checkedTraitAliases[$owner->getName()] = true;
            }
        }
    }

    /** @return array<string, array<string, ClassReflection>> */
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
                        $this->livewireTraits[$trait->getName()][$class->getName()] = $class;
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
        $this->checkedTraitAliases = [];
    }
}
