<?php

namespace Filament\Commands\FileGenerators;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;

class PanelProviderClassGenerator extends ClassGenerator
{
    final public function __construct(
        protected string $fqn,
        protected string $id,
        protected bool $isDefault = false,
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
        return [
            Panel::class,
            $this->getExtends(),
            Color::class,
            Dashboard::class,
            AccountWidget::class,
            FilamentInfoWidget::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            Authenticate::class,
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return PanelProvider::class;
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addPanelMethodToClass($class);
    }

    protected function addPanelMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('panel')
            ->setPublic()
            ->setReturnType(Panel::class)
            ->setBody($this->generatePanelMethodBody());
        $method->addParameter('panel')
            ->setType(Panel::class);

        $this->configurePanelMethod($method);
    }

    public function generatePanelMethodBody(): string
    {
        $isDefault = $this->isDefault();

        $defaultOutput = $isDefault
            ? <<<'PHP'

                    ->default()
                PHP
            : '';

        $loginOutput = $isDefault
            ? <<<'PHP'

                    ->login()
                PHP
            : '';

        $id = $this->getId();

        $componentsDirectory = $isDefault ? '' : (Str::studly($id) . '/');
        $componentsNamespace = $isDefault ? '' : (Str::studly($id) . '\\');

        $rootNamespace = app()->getNamespace();

        return new Literal(
            <<<PHP
                return \$panel{$defaultOutput}
                    ->id(?)
                    ->path(?){$loginOutput}
                    ->colors([
                        'primary' => {$this->simplifyFqn(Color::class)}::Amber,
                    ])
                    ->discoverResources(in: app_path('Filament/{$componentsDirectory}Resources'), for: '{$rootNamespace}Filament\\{$componentsNamespace}Resources')
                    ->discoverPages(in: app_path('Filament/{$componentsDirectory}Pages'), for: '{$rootNamespace}Filament\\{$componentsNamespace}Pages')
                    ->pages([
                        {$this->simplifyFqn(Dashboard::class)}::class,
                    ])
                    ->discoverWidgets(in: app_path('Filament/{$componentsDirectory}Widgets'), for: '{$rootNamespace}Filament\\{$componentsNamespace}Widgets')
                    ->widgets([
                        {$this->simplifyFqn(AccountWidget::class)}::class,
                        {$this->simplifyFqn(FilamentInfoWidget::class)}::class,
                    ])
                    ->middleware([
                        {$this->simplifyFqn(EncryptCookies::class)}::class,
                        {$this->simplifyFqn(AddQueuedCookiesToResponse::class)}::class,
                        {$this->simplifyFqn(StartSession::class)}::class,
                        {$this->simplifyFqn(AuthenticateSession::class)}::class,
                        {$this->simplifyFqn(ShareErrorsFromSession::class)}::class,
                        {$this->simplifyFqn(VerifyCsrfToken::class)}::class,
                        {$this->simplifyFqn(SubstituteBindings::class)}::class,
                        {$this->simplifyFqn(DisableBladeIconComponents::class)}::class,
                        {$this->simplifyFqn(DispatchServingFilamentEvent::class)}::class,
                    ])
                    ->authMiddleware([
                        {$this->simplifyFqn(Authenticate::class)}::class,
                    ]);
                PHP,
            [$id, $id],
        );
    }

    protected function configurePanelMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
