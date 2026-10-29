<?php

namespace Filament\Upgrade;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ConfigurationClassAnalyzer
{
    /** @var array<string, array<string, string>> */
    private array $parents = [];

    /** @var array<string, array<string, array<string, true>>> */
    private array $references = [];

    public function hasDescendants(string $className): bool
    {
        return in_array(strtolower($className), $this->parents[$this->loadSources()], true);
    }

    /** @return list<string> */
    public function getDescendants(string $className): array
    {
        $parents = $this->parents[$this->loadSources()];
        $descendants = [];
        $className = strtolower($className);

        foreach ($parents as $candidate => $parent) {
            while ($parent) {
                if ($parent === $className) {
                    $descendants[] = $candidate;

                    break;
                }

                $parent = $parents[$parent] ?? null;
            }
        }

        return $descendants;
    }

    /** @param array<Node> $statements */
    public function hasExternalClassReferences(string $className, array $statements): bool
    {
        $references = $this->references[$this->loadSources()];

        foreach ($statements as $statement) {
            $this->collectReferences($statement, '', $references);
        }

        $className = strtolower($className);

        // Public hook callers cannot simply be renamed: the replacement may
        // resolve a default instead of returning the original nullable value.
        // Even a type or registration reference can pass the class to callers
        // elsewhere, so leave these public extension points for reconciliation.
        return (bool) array_diff(array_keys($references[$className] ?? []), [$className]);
    }

    private function loadSources(): string
    {
        // Inspect configured paths even during a narrowed CLI run. A descendant
        // in another file can change the meaning of an inherited hook or read.
        $paths = SimpleParameterProvider::provideArrayParameter(Option::PATHS);

        $key = implode("\n", $paths);

        if (! isset($this->parents[$key])) {
            $parents = [];
            $references = [];
            $parser = (new ParserFactory)->createForNewestSupportedVersion();

            foreach ($paths as $path) {
                $files = is_dir($path) ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)) : [$path];

                foreach ($files as $file) {
                    $file = (string) $file;

                    if ((! is_file($file)) || (! str_ends_with($file, '.php'))) {
                        continue;
                    }

                    $statements = (new NodeTraverser(new NameResolver))->traverse($parser->parse(file_get_contents($file)) ?? []);

                    foreach ($statements as $statement) {
                        $this->collectReferences($statement, '', $references);
                    }

                    foreach ((new NodeFinder)->findInstanceOf($statements, Class_::class) as $class) {
                        if ($class->extends) {
                            $parents[strtolower($class->namespacedName?->toString() ?? $file . ':' . $class->getStartLine())] = strtolower($class->extends->toString());
                        }
                    }
                }
            }

            $this->parents[$key] = $parents;
            $this->references[$key] = $references;
        }

        return $key;
    }

    /** @param array<string, array<string, true>> $references */
    private function collectReferences(Node $node, string $owner, array &$references): void
    {
        if ($node instanceof Class_) {
            $owner = strtolower($node->namespacedName?->toString() ?? '');
        }

        if ($node instanceof Name || $node instanceof String_) {
            $name = $node instanceof Name ? $node->toString() : explode('::', $node->value, 2)[0];
            $references[strtolower(ltrim($name, '\\'))][$owner] = true;
        }

        foreach ($node->getSubNodeNames() as $subNodeName) {
            $children = is_array($node->$subNodeName) ? $node->$subNodeName : [$node->$subNodeName];

            foreach ($children as $child) {
                if ($child instanceof Node) {
                    $this->collectReferences($child, $owner, $references);
                }
            }
        }
    }
}
