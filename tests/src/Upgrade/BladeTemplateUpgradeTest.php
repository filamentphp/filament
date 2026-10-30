<?php

namespace Filament\Tests\Upgrade;

use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Process\Process;

class BladeTemplateUpgradeTest extends TestCase
{
    public function test_migrates_literal_references_without_changing_computed_names_or_source_examples(): void
    {
        [$original, $expected] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/Blade/legacy_aliases.blade.php.inc'), 2);
        $this->assertSame($expected, $this->migrate($original));

        $original = <<<'BLADE'
            <x-dynamic-component component='filament::card' />
            <x:dynamic-component :component="'filament::card'" />
            <x-dynamic-component :component=' "filament::widget" ' />
            <x-dynamic-component bind:component="'filament::card'" />
            <x-dynamic-component component="{{ 'filament::card' }}" />
            <x-dynamic-component component="{{{ 'filament::card' }}}" />
            <x-dynamic-component component="{!! 'filament::card' !!}" />
            <x-dynamic-component data-example='component="filament::card"' component="filament::card" />
            BLADE;
        $expected = str_replace(
            ["component='filament::card'", "component=\"'filament::card'\"", "component=' \"filament::widget\" '", 'component="filament::card" />'],
            ["component='filament::section'", "component=\"'filament::section'\"", "component=' \"filament-widgets::widget\" '", 'component="filament::section" />'],
            $original,
        );
        $expected = str_replace(["{{ 'filament::card' }}", "{{{ 'filament::card' }}}", "{!! 'filament::card' !!}"], ["{{ 'filament::section' }}", "{{{ 'filament::section' }}}", "{!! 'filament::section' !!}"], $expected);
        $this->assertSame($expected, $this->migrate($original));

        $unchanged = <<<'BLADE'
            <x-dynamic-component :component="$component" />
            <x-dynamic-component :component="'filament::card' . $suffix" />
            <x-dynamic-component component="{{ 'filament::card' . $suffix }}" />
            <x-dynamic-component ::component="'filament::card'" />
            <x-dynamic-component @class([' component="filament::card" ']) :component="$component" />
            <div component="filament::card" data-template="@include('filament::components.card')"></div>
            @include('filament::components.card', ['attributes' => new \Illuminate\View\ComponentAttributeBag(['heading' => 'Heading', 'heading-tag' => 'h3'])])
            @includeIf("filament::components.card")
            @includeIsolated('filament::components.card')
            @includeWhen(count(array_filter($items, fn ($item) => $item->active)) > 0, 'filament::components.card')
            @includeUnless($hidden, 'filament::components.card')
            @extends('filament::components.card')
            @component('filament::components.card')Content@endcomponent
            @each('filament::components.card', $items, 'item')
            @each('custom', $items, 'item', 'filament::components.card')
            @includeFirst(['filament::components.card', 'fallback'])
            @extendsFirst(['filament::components.card'])
            @componentFirst(['filament::components.card'])
            {!! view('filament::components.card', ['example' => 'filament::components.card'])->render() !!}
            {{ \view("filament::components.card") }}
            @include('filament::components.card' . $suffix)
            @include($view, ['example' => 'filament::components.card'])
            @includeWhen('filament::components.card', $view)
            @includeFirst(['filament::components.card' . $suffix, $view], ['example' => 'filament::components.card'])
            @include('filament::card')
            @include('filament::components.page')
            {{ 'filament::components.card' }}
            {{ "<x-filament::card />" }}
            {{ "@include('filament::components.card')" }}
            {{ "}} <x-filament::card />" }}
            {!! '!!} <x-filament::card />' !!}
            {{ $object->view('filament::components.card') }}
            {{ Other::view('filament::components.card') }}
            {!! view('filament::components.card' . $suffix) !!}
            @@include('filament::components.card')
            @{{ view('filament::components.card') }}
            {{-- @include('filament::components.card') <x-dynamic-component component="filament::card" /> --}}
            <!-- @include('filament::components.card') -->
            @verbatim
            @include('filament::components.card') <x-dynamic-component component="filament::card" />
            @endverbatim
            @php
                $example = "@include('filament::components.card') <x-filament::card />";
            @endphp
            <?php $example = '<x-filament::card />'; ?>
            <?php $example = '?> <x-filament::card />'; ?>
            BLADE;
        $this->assertSame($unchanged, $this->migrate($unchanged));
    }

    public function test_migrated_card_components_preserve_kebab_case_props_when_rendered(): void
    {
        $expected = Blade::render('<x-filament::section heading="Heading" heading-tag="h3">Content</x-filament::section>');
        $this->assertStringContainsString('<h3', $expected);

        foreach ([
            '<x-filament::card heading="Heading" heading-tag="h3">Content</x-filament::card>',
            '<x-dynamic-component component="filament::card" heading="Heading" heading-tag="h3">Content</x-dynamic-component>',
            '<x-dynamic-component :component="\'filament::card\'" heading="Heading" heading-tag="h3">Content</x-dynamic-component>',
            '<x-dynamic-component bind:component="\'filament::card\'" heading="Heading" heading-tag="h3">Content</x-dynamic-component>',
            '<x-dynamic-component component="{{ \'filament::card\' }}" heading="Heading" heading-tag="h3">Content</x-dynamic-component>',
            '<x-dynamic-component component="{{{ \'filament::card\' }}}" heading="Heading" heading-tag="h3">Content</x-dynamic-component>',
            '<x-dynamic-component component="{!! \'filament::card\' !!}" heading="Heading" heading-tag="h3">Content</x-dynamic-component>',
        ] as $template) {
            $this->assertSame($expected, Blade::render($this->migrate($template)));
        }
    }

    public function test_migrates_components_executed_inside_html_comments_but_not_blade_comments(): void
    {
        $original = <<<'BLADE'
            <!--
            @if ($enabled)
                <x-filament::card heading="Heading" heading-tag="h3">Content</x-filament::card>
                <x-dynamic-component component="filament::card" heading="Dynamic heading" heading-tag="h3">Dynamic content</x-dynamic-component>
            @endif
            -->
            {{-- <x-filament::card /> <x-dynamic-component component="filament::card" /> @include('missing-view') --}}
            BLADE;
        $expected = str_replace(['<x-filament::card heading=', '</x-filament::card>', 'component="filament::card" heading='], ['<x-filament::section heading=', '</x-filament::section>', 'component="filament::section" heading='], $original);
        $migrated = $this->migrate($original);
        $this->assertSame($expected, $migrated);
        $rendered = Blade::render($migrated, ['enabled' => true]);
        $this->assertSame(Blade::render($expected, ['enabled' => true]), $rendered);
        $this->assertSame(2, substr_count($rendered, '<section'));
        $this->assertSame(2, substr_count($rendered, '<h3'));
        $this->assertStringContainsString('Dynamic content', $rendered);
        $this->assertStringContainsString('<!--', $rendered);
        $this->assertStringContainsString('-->', $rendered);
        $this->assertStringNotContainsString('@if', $rendered);
        $this->assertStringNotContainsString('<section', Blade::render($migrated, ['enabled' => false]));
        $this->assertSame('', Blade::render('{{-- <x-filament::card /> @include("missing-view") --}}'));
    }

    private function migrate(string $original): string
    {
        $directory = sys_get_temp_dir() . '/filament-blade-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $path = $directory . '/example.blade.php';
        file_put_contents($path, $original);

        foreach (['vendor', 'node_modules', '.git'] as $excludedDirectory) {
            mkdir($directory . '/' . $excludedDirectory);
            file_put_contents($directory . '/' . $excludedDirectory . '/example.blade.php', $original);
        }

        $externalDirectory = $directory . '-external';
        mkdir($externalDirectory);
        file_put_contents($externalDirectory . '/example.blade.php', $original);
        symlink($externalDirectory, $directory . '/linked-directory');
        symlink($externalDirectory . '/example.blade.php', $directory . '/linked-file.blade.php');
        file_put_contents($directory . '/example.php', $original);

        try {
            $process = $this->scan($directory, dryRun: true);
            $dryRunExitCode = $process->getExitCode();
            $dryRunOutput = $process->getOutput();
            $this->assertSame($original, file_get_contents($path));
            $process = $this->scan($directory);
            $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
            $migrated = file_get_contents($path);
            $this->assertSame(($original === $migrated) ? 0 : 2, $dryRunExitCode);
            $this->assertSame(($original === $migrated) ? '' : "Would update: {$path}\n", $dryRunOutput);
            $process = $this->scan($directory, dryRun: true);
            $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
            $this->assertSame('', $process->getOutput());
            $this->assertSame($migrated, file_get_contents($path));
            $process = $this->scan($directory);
            $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
            $this->assertSame('', $process->getOutput());

            foreach (['vendor', 'node_modules', '.git'] as $excludedDirectory) {
                $this->assertSame($original, file_get_contents($directory . '/' . $excludedDirectory . '/example.blade.php'));
            }

            $this->assertSame($original, file_get_contents($externalDirectory . '/example.blade.php'));
            $this->assertSame($original, file_get_contents($directory . '/example.php'));
            $this->assertSame('', $this->scan($directory . '/linked-directory')->getOutput());

            return $migrated;
        } finally {
            unlink($directory . '/linked-directory');
            unlink($directory . '/linked-file.blade.php');
            (new Filesystem)->deleteDirectory($directory);
            (new Filesystem)->deleteDirectory($externalDirectory);
        }
    }

    private function scan(string $directory, bool $dryRun = false): Process
    {
        $process = new Process([PHP_BINARY, dirname(__DIR__, 3) . '/packages/upgrade/src/upgrade-blade-templates.php', $directory, ...($dryRun ? ['--dry-run'] : [])]);
        $process->run();

        return $process;
    }
}
