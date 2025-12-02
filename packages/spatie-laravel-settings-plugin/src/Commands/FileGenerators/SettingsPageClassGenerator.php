<?php

namespace Filament\Commands\FileGenerators;

use BackedEnum;
use DateTimeInterface;
use Filament\Clusters\Cluster;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class SettingsPageClassGenerator extends ClassGenerator
{
    /**
     * @param  class-string  $settingsFqn
     * @param  ?class-string<Cluster>  $clusterFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $settingsFqn,
        protected ?string $clusterFqn,
        protected bool $isGenerated,
    ) {}

    public function getNamespace(): string
    {
        return $this->extractNamespace($this->getFqn());
    }

    /**
     * @return array<string>
     */
    public function getImports(): array
    {
        $extends = $this->getExtends();
        $extendsBasename = class_basename($extends);

        return [
            ...(($extendsBasename === class_basename($this->getFqn())) ? [$extends => "Base{$extendsBasename}"] : [$extends]),
            $this->getSettingsFqn(),
            ...($this->hasCluster() ? (($this->getClusterBasename() === 'SettingsPage') ? [$this->getClusterFqn() => 'SettingsPageCluster'] : [$this->getClusterFqn()]) : []),
            Schema::class,
            ...($this->hasPartialImports() ? ['Filament\Forms'] : []),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return SettingsPage::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addNavigationIconPropertyToClass($class);
        $this->addSettingsPropertyToClass($class);
        $this->addClusterPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addFormMethodToClass($class);
    }

    protected function addNavigationIconPropertyToClass(ClassType $class): void
    {
        $this->namespace->addUse(BackedEnum::class);
        $this->namespace->addUse(Heroicon::class);

        $property = $class->addProperty('navigationIcon', new Literal('Heroicon::OutlinedCog6Tooth'))
            ->setProtected()
            ->setStatic()
            ->setType('string|BackedEnum|null');
        $this->configureNavigationIconProperty($property);
    }

    protected function configureNavigationIconProperty(Property $property): void {}

    protected function addSettingsPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('settings', new Literal("{$this->simplifyFqn($this->getSettingsFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('string');
        $this->configureSettingsProperty($property);
    }

    protected function configureSettingsProperty(Property $property): void {}

    protected function addClusterPropertyToClass(ClassType $class): void
    {
        if (! $this->hasCluster()) {
            return;
        }

        $property = $class->addProperty('cluster', new Literal("{$this->simplifyFqn($this->getClusterFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureClusterProperty($property);
    }

    protected function configureClusterProperty(Property $property): void {}

    protected function addFormMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('form')
            ->setPublic()
            ->setReturnType(Schema::class)
            ->setBody($this->generateFormMethodBody());
        $method->addParameter('schema')
            ->setType(Schema::class);

        $this->configureFormMethod($method);
    }

    public function generateFormMethodBody(): string
    {
        return <<<PHP
            return \$schema
                ->components([
                    {$this->outputFormComponents()}
                ]);
            PHP;
    }

    /**
     * @return array<string>
     */
    public function getFormComponents(): array
    {
        if (! $this->isGenerated()) {
            return [];
        }

        $settingsFqn = $this->getSettingsFqn();

        if (! class_exists($settingsFqn)) {
            return [];
        }

        $components = [];

        foreach ((new ReflectionClass($settingsFqn))->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (! $property->isPublic()) {
                continue;
            }

            $componentName = $property->getName();

            $componentData = [];

            $propertyType = $property->getType();

            $isDateTimeType = fn (ReflectionType $type): bool => $type instanceof ReflectionNamedType && (class_exists($type->getName()) || interface_exists($type->getName())) && (is_subclass_of($type->getName(), DateTimeInterface::class) || $type->getName() === DateTimeInterface::class);
            $isEnumType = fn (ReflectionType $type): bool => $type instanceof ReflectionNamedType && enum_exists($type->getName());

            $componentData['type'] = match (true) {
                $propertyType instanceof ReflectionNamedType && $propertyType->getName() === 'bool' => Toggle::class,
                $isDateTimeType($propertyType) || ($propertyType instanceof ReflectionUnionType && collect($propertyType->getTypes())->contains($isDateTimeType)) => DateTimePicker::class,
                str($componentName)->contains('image', ignoreCase: true) => FileUpload::class,
                $isEnumType($propertyType) || ($propertyType instanceof ReflectionUnionType && collect($propertyType->getTypes())->contains($isEnumType)) => Select::class,
                default => TextInput::class,
            };

            if (in_array($componentName, [
                'id',
                'sku',
                'uuid',
            ])) {
                $componentData['label'] = [Str::upper($componentName)];
            }

            if ($componentData['type'] === TextInput::class) {
                if (str($componentName)->contains(['email'])) {
                    $componentData['email'] = [];
                }

                if (str($componentName)->contains(['password'])) {
                    $componentData['password'] = [];
                }

                if (str($componentName)->contains(['phone', 'tel'])) {
                    $componentData['tel'] = [];
                }
            }

            if ($componentData['type'] === FileUpload::class) {
                $componentData['image'] = [];
            }

            $isFloatType = fn (ReflectionType $type): bool => $type instanceof ReflectionNamedType && $type->getName() === 'float';
            $isIntegerType = fn (ReflectionType $type): bool => $type instanceof ReflectionNamedType && $type->getName() === 'int';

            $setUpNumericComponentData = function () use (&$componentData, $componentName): void {
                $componentData['numeric'] = [];

                if (in_array($componentName, [
                    'cost',
                    'money',
                    'price',
                ]) || str($componentName)->endsWith([
                    '_cost',
                    '_price',
                ])) {
                    $componentData['prefix'] = ['$'];
                }
            };

            if ($isFloatType($propertyType) || ($propertyType instanceof ReflectionUnionType && collect($propertyType->getTypes())->contains($isFloatType))) {
                $setUpNumericComponentData();
            } elseif ($isIntegerType($propertyType) || ($propertyType instanceof ReflectionUnionType && collect($propertyType->getTypes())->contains($isIntegerType))) {
                $setUpNumericComponentData();
                $componentData['integer'] = [];
            }

            if ($isEnumType($propertyType) || ($propertyType instanceof ReflectionUnionType && collect($propertyType->getTypes())->contains($isEnumType))) {
                $enumClass = $isEnumType($propertyType)
                    ? ($propertyType instanceof ReflectionNamedType ? $propertyType->getName() : '')
                    : ($propertyType instanceof ReflectionUnionType ? collect($propertyType->getTypes())->first($isEnumType)->getName() : '');

                $this->namespace->addUse($enumClass);

                $componentData['options'] = [new Literal("{$this->simplifyFqn($enumClass)}::class")];
            }

            if (! $propertyType->allowsNull()) {
                $componentData['required'] = [];
            }

            $this->importUnlessPartial($componentData['type']);

            $components[$componentName] = $componentData;
        }

        return array_map(
            function (array $componentData, string $componentName): string {
                $component = (string) new Literal("{$this->simplifyFqn($componentData['type'])}::make(?)", [$componentName]);

                unset($componentData['type']);

                foreach ($componentData as $methodName => $parameters) {
                    $component .= new Literal(PHP_EOL . "            ->{$methodName}(...?:)", [$parameters]);
                }

                return "{$component},";
            },
            $components,
            array_keys($components),
        );
    }

    public function outputFormComponents(): string
    {
        $components = $this->getFormComponents();

        if (empty($components)) {
            return '//';
        }

        return implode(PHP_EOL . '        ', $components);
    }

    protected function configureFormMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * @return class-string
     */
    public function getSettingsFqn(): string
    {
        return $this->settingsFqn;
    }

    /**
     * @return ?class-string<Cluster>
     */
    public function getClusterFqn(): ?string
    {
        return $this->clusterFqn;
    }

    public function getClusterBasename(): string
    {
        return class_basename($this->getClusterFqn());
    }

    public function hasCluster(): bool
    {
        return filled($this->getClusterFqn());
    }

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }
}
