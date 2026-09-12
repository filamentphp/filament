<?php

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

// Publishes assets into the same public directory used by other browser tests.
uses(TestCase::class)->group('serial');

dataset('JS field frameworks', ['react', 'vue', 'svelte']);

beforeAll(function (): void {
    if (! is_file(dirname(__DIR__, 4) . '/build/js-field-renderers/react.js')) {
        throw new LogicException('Run `npm run test:js` before the framework browser tests.');
    }
});

beforeEach(function (): void {
    $directory = dirname(__DIR__, 4) . '/build/js-field-renderers';
    foreach (File::allFiles($directory) as $file) {
        $id = str_replace('\\', '/', $file->getRelativePathname());
        $id = substr($id, 0, -(strlen($file->getExtension()) + 1));
        FilamentAsset::register([
            $file->getExtension() === 'css'
                ? Css::make($id, $file->getPathname())
                : Js::make($id, $file->getPathname())->loadedOnRequest(),
        ], 'tests/js-fields');
    }
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('calls exposed field methods and the owning `$wire` across nested instances and remounts', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $report = "JSON.parse(document.querySelectorAll('[data-field=nested] [data-report]')[1].textContent || 'null')";
    $page->click('[data-blade-method=blade] button:has-text("Named Blade call")')
        ->assertSeeIn('[data-blade-method=blade] output', 'data.blade: Named café')
        ->assertScript("{$state}.blade", 'Named café')
        ->click('[data-blade-method=flex_blade] button:has-text("Named Blade call")')
        ->assertSeeIn('[data-blade-method=flex_blade] output', 'data.flex_blade: Named café')
        ->click('[data-blade-method=blade] button:has-text("General Blade call")')
        ->assertScript("{$state}.blade", 'General café')
        ->assertScript("{$state}.flex_blade", 'Named café');
    $page->fill('[id="form.deferred"]', 'Deferred before call')
        ->click('[data-field=nested] button:has-text("Call field method") >> nth=1')
        ->assertScript($report, ['path' => 'data.items.1.field', 'previous' => 'Second row', 'title' => 'PHP café replacement'])
        ->assertValue('[id="form.items.1.field"]', 'PHP café replacement')
        ->assertValue('[id="form.items.0.field"]', 'Original café')
        ->assertScript("{$state}.deferred.title", 'Deferred before call')
        ->assertScript("{$state}.items[1].field", ['title' => 'PHP café replacement', 'enabled' => false, 'tags' => ['push']]);

    $page->script('() => { window.rendered = null; Livewire.hook("commit", ({ succeed }) => succeed(({ effects }) => { window.rendered = !!effects.html })); }');
    $page->click('[data-field=nested] button:has-text("Call renderless method") >> nth=1')
        ->assertScript($report, 'Read: PHP café replacement')
        ->assertScript('window.rendered', false)
        ->fill('[id="form.caption"]', 'Pending renderless caption')
        ->click('[data-field=nested] button:has-text("Call renderless method") >> nth=1')
        ->assertScript($report, 'Read: PHP café replacement')
        ->assertScript('window.rendered', true)
        ->assertScript("{$state}.caption", 'Pending renderless caption')
        ->click('[data-field=nested] button:has-text("Call unexposed method") >> nth=1')
        ->assertScript($report, null)
        ->assertScript("{$state}.items[1].field.title", 'PHP café replacement')
        ->click('[data-field=deferred] button:has-text("Call Livewire method")')
        ->assertScript("JSON.parse(document.querySelector('[data-field=deferred] [data-report]').textContent || 'null')", ['caption' => 'Via $wire café', 'framework' => $framework])
        ->assertValue('[id="form.caption"]', 'Via $wire café')
        ->assertScript("{$state}.caption", 'Via $wire café');

    $page->click('Remove first')->fill('[id="form.items.1.field"]', 'Survivor')
        ->click('[data-field=nested] button:has-text("Call field method")')
        ->assertScript("{$state}.items[1].field.title", 'PHP café replacement')
        ->click('Toggle mounted')->assertNotPresent('[id="form.items.1.field"]')
        ->click('Toggle mounted')->assertValue('[id="form.items.1.field"]', 'PHP café replacement')
        ->fill('[id="form.items.1.field"]', 'Remounted')
        ->assertScript("{$state}.items[1].field.title", 'Remounted')
        ->click('[data-field=nested] button:has-text("Call field method")')
        ->assertScript("JSON.parse(document.querySelector('[data-field=nested] [data-report]').textContent || 'null')", ['path' => 'data.items.1.field', 'previous' => 'Remounted', 'title' => 'PHP café replacement'])
        ->assertScript('document.documentElement.classList.contains("dark")', $dark)
        ->assertNoSmoke();
    $page->screenshotElement('[data-field=nested]', 'js-field-methods-' . $framework . ($dark ? '-dark' : '-light'));
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('synchronizes composite state with deferred, live, blur and debounce bindings', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertValue('[id="form.deferred"]', 'Original café');
    $page->assertScript('[...document.querySelectorAll("[data-field=deferred] [x-ref=host] *")].map(element => element.tagName)', ['DIV', 'INPUT', 'LABEL', 'INPUT', 'BUTTON', 'OUTPUT', 'OUTPUT', 'BUTTON', 'OUTPUT', 'BUTTON', 'BUTTON', 'BUTTON', 'BUTTON']);
    $page->script('() => { window.requests = 0; Livewire.hook("request", () => window.requests++); }');

    $page->fill('[id="form.deferred"]', 'Edited "café" <em>text</em>')
        ->uncheck('[data-field="deferred"] input[type="checkbox"]')
        ->click('[data-field="deferred"] button:has-text("Toggle channels")')
        ->wait(0.6)->assertScript('window.requests', 0)
        ->assertScript("{$state}.deferred", ['title' => 'Original café', 'enabled' => true, 'tags' => ['email']])
        ->assertScript("Alpine.\$data(document.querySelector('[data-field=deferred]')).state", ['title' => 'Edited "café" <em>text</em>', 'enabled' => false, 'tags' => ['sms', 'push']])
        ->click('Sync')
        ->assertScript("{$state}.deferred", ['title' => 'Edited "café" <em>text</em>', 'enabled' => false, 'tags' => ['sms', 'push']])
        ->assertValue('[id="form.live"]', 'Original café')
        ->fill('[id="form.live"]', 'Live edit')
        ->assertScript("{$state}.live.title", 'Live edit');

    $requests = $page->script('window.requests');
    $page->fill('[id="form.blur"]', 'Blur edit')->wait(0.6)
        ->assertScript('window.requests', $requests)
        ->assertScript("{$state}.blur.title", 'Original café')
        ->click('#framework-server-state')->assertScript("{$state}.blur.title", 'Blur edit')
        ->fill('[id="form.debounce"]', 'First edit')
        ->fill('[id="form.debounce"]', 'Last edit')
        ->assertScript("{$state}.debounce.title", 'Original café')
        ->assertScript("{$state}.debounce.title", 'Last edit');

    $page->click('Replace from server');
    foreach (['deferred', 'live', 'blur', 'debounce'] as $name) {
        $page->assertValue('[id="form.' . $name . '"]', '')
            ->assertNotChecked('[data-field="' . $name . '"] input[type="checkbox"]')
            ->assertSeeIn('[data-field="' . $name . '"] [data-channels]', '[]');
    }
    $page->click('Reset')->assertValue('[id="form.live"]', 'Original café')
        ->assertChecked('[data-field="live"] input[type="checkbox"]')
        ->assertSeeIn('[data-field="live"] [data-channels]', '["email"]');
    $requests = $page->script('window.requests');
    $page->wait(0.7)->assertScript('window.requests', $requests)->assertNoSmoke()
        ->assertScript('document.documentElement.classList.contains("dark")', $dark);
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('contains initialization failures without losing state or breaking other framework instances', function (string $framework, string $failure, bool $dark): void {
    $page = visit('/js-field-framework-test?scenario=failure&framework=' . $framework . '&failure=' . $failure);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertVisible('[data-field="unavailable"] [role="alert"]')
        ->assertSee('This field could not be loaded. Please reload the page to try again.')
        ->assertScript("document.querySelector('[data-field=unavailable] [x-ref=host]').childElementCount", 0)
        ->assertScript("{$state}.unavailable", ['title' => 'Original café', 'enabled' => true, 'tags' => ['email']])
        ->fill('[id="form.live"]', 'Still working')
        ->assertScript("{$state}.live.title", 'Still working')
        ->assertScript("{$state}.unavailable", ['title' => 'Original café', 'enabled' => true, 'tags' => ['email']])
        ->assertScript('document.querySelectorAll("[x-ref=host] input").length', 2)
        ->assertScript('document.getAnimations().every(animation => animation.playState !== "running" || animation.effect.getTiming().iterations === Infinity)', true)
        ->assertScript('document.documentElement.classList.contains("dark")', $dark);
    $page->script('document.querySelector("[data-field=unavailable]").scrollIntoView({ block: "center", behavior: "instant" })');
    $page->assertVisible('[data-field="unavailable"] [role="alert"]')
        ->assertNoAccessibilityIssues();
})->with('JS field frameworks')->with(['missing', 'export', 'mount'])->with(['light' => false, 'dark' => true]);

it('updates PHP props and nested schema utilities without remounting framework state', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $configuration = "JSON.parse(document.querySelector('[data-field=deferred] [data-config]').textContent)";
    $page->assertValue('[id="form.deferred"]', 'Original café');
    $page->assertScript('document.querySelectorAll("[x-ref=host] input").length', 12);
    $page->script('() => { window.inputs = [...document.querySelectorAll("[x-ref=host] input")]; }');
    $page->assertScript("{$configuration}.caption", 'Root "caption" <em>literal</em>')
        ->assertScript("{$configuration}.nested", ['quote' => '"', 'html' => '<em>literal</em>'])
        ->assertNotPresent('[x-ref="host"] em')
        ->fill('[id="form.caption"]', 'New "props" <script>literal</script>')
        ->assertScript("{$configuration}.caption", 'Root "caption" <em>literal</em>')
        ->click('Sync')->assertScript("{$configuration}.caption", 'New "props" <script>literal</script>')
        ->assertScript('window.inputs.every((input, index) => input === document.querySelectorAll("[x-ref=host] input")[index])', true)
        ->fill('[id="form.caption"]', '')->click('Sync')->assertScript($configuration, []);

    $page->fill('[id="form.caption"]', 'Restored root')->click('Sync')
        ->click('[data-field="nested"] button:has-text("Use utilities") >> nth=1')
        ->assertScript("{$state}.items[1].caption", 'Changed by renderer')
        ->assertScript("{$state}.caption", 'Changed root by renderer')
        ->assertValue('[id="form.items.0.caption"]', 'First caption');
    $report = "JSON.parse(document.querySelectorAll('[data-field=nested] [data-report]')[1].textContent)";
    $page->assertScript($report, [
        'path' => 'data.items.1.field',
        'state' => ['title' => 'Second row', 'enabled' => false, 'tags' => ['push']],
        'sibling' => 'Second caption',
        'root' => 'Restored root',
        'relative' => 'Restored root',
    ]);
    $page->fill('[id="form.items.1.field"]', 'Nested edit')
        ->assertScript("{$state}.items[1].field.title", 'Nested edit')
        ->click('Sync')->assertScript("{$report}.state.title", 'Second row')
        ->assertScript('window.inputs.every((input, index) => input === document.querySelectorAll("[x-ref=host] input")[index])', true)
        ->assertScript('document.documentElement.classList.contains("dark")', $dark)
        ->assertNoSmoke();
    $page->screenshotElement('[data-field="nested"] >> nth=1', 'js-field-' . $framework . ($dark ? '-dark' : '-light'));
    $page->click('[data-field="nested"] button:has-text("Use utilities") >> nth=1')
        ->assertScript("{$report}.state.title", 'Nested edit')
        ->assertScript("{$report}.root", 'Changed root by renderer');
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('lazy loads one module for independent instances and cleans up on removal and remount', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?unmounted=1&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $loads = "performance.getEntriesByType('resource').filter(entry => new URL(entry.name).pathname === '/js/tests/js-fields/{$framework}.js').length";
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertScript($loads, 0)->click('Toggle mounted')
        ->assertValue('[id="form.items.1.field"]', 'Second row')
        ->assertScript($loads, 1);
    $page->assertScript("performance.getEntriesByType('resource').some(entry => new URL(entry.name).pathname.startsWith('/js/tests/js-fields/chunks/'))", true)
        ->assertScript("performance.getEntriesByType('resource').some(entry => new URL(entry.name).pathname === '/css/tests/js-fields/{$framework}.css')", true)
        ->assertScript("getComputedStyle(document.querySelector('[data-field=live] [x-ref=host] > div')).display", 'grid');
    $page->script('() => { window.firstHost = document.getElementById("form.items.0.field").closest("[x-ref=host]"); window.survivor = document.getElementById("form.items.1.field"); }');
    $page->click('Remove first')->assertNotPresent('[id="form.items.0.field"]')
        ->assertScript('window.firstHost.childElementCount', 0)
        ->assertScript('document.getElementById("form.items.1.field") === window.survivor', true)
        ->fill('[id="form.items.1.field"]', 'Survived')
        ->assertScript("{$state}.items[1].field.title", 'Survived')
        ->click('Toggle locked')->assertDisabled('[id="form.live"]')
        ->assertAttribute('[id="form.blur"]', 'readonly', '')
        ->click('Toggle locked')->assertEnabled('[id="form.live"]');

    $page->script('() => { window.hosts = [...document.querySelectorAll("[x-ref=host]")]; window.requests = 0; Livewire.hook("request", () => window.requests++); }');
    $page->fill('[id="form.debounce"]', 'Pending removal')->click('Toggle mounted')
        ->assertNotPresent('[id="form.debounce"]')
        ->assertScript('window.hosts.every(host => host.childElementCount === 0)', true);
    $requests = $page->script('window.requests');
    $page->wait(0.7)->assertScript('window.requests', $requests)
        ->click('Replace from server')->click('Toggle mounted')
        ->assertValue('[id="form.debounce"]', '')
        ->assertValue('[id="form.items.1.field"]', 'Survived')
        ->assertScript($loads, 1)
        ->fill('[id="form.live"]', 'Remounted edit')->assertScript("{$state}.live.title", 'Remounted edit')
        ->assertScript('document.documentElement.classList.contains("dark")', $dark)
        ->assertNoSmoke();
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('associates helper and validation text with each framework input', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?scenario=description&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $description = "document.getElementById(document.getElementById('form.live').getAttribute('aria-describedby')).textContent";
    $page->assertAttribute('[id="form.live"]', 'aria-describedby', 'form.live-description')
        ->assertScript("{$description}.includes('Choose a title and notification channels.')", true)
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertScript("{$description}.includes('Choose a different title.')", true)
        ->assertScript('document.documentElement.classList.contains("dark")', $dark)
        ->assertNoAccessibilityIssues();
    $page->screenshotElement('[data-field-wrapper]:has([id="form.live"])', 'js-field-description-' . $framework . ($dark ? '-dark' : '-light'));
    $page->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'false')
        ->assertScript("{$description}.includes('Choose a different title.')", false)
        ->assertScript("{$description}.includes('Choose a title and notification channels.')", true)
        ->assertNoSmoke();
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('respects a conditional `live(onBlur: true)` binding', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?deferredBlur=1&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertValue('[id="form.blur"]', 'Original café');
    $page->script('() => { window.requests = 0; Livewire.hook("request", () => window.requests++); }');
    $page->fill('[id="form.blur"]', 'Deferred despite blur')
        ->click('#framework-server-state')->wait(0.6)
        ->assertScript('window.requests', 0)
        ->assertScript("{$state}.blur.title", 'Original café')
        ->click('Toggle blur binding')
        ->assertScript("{$state}.blur.title", 'Deferred despite blur')
        ->fill('[id="form.blur"]', 'Now live on blur')
        ->assertScript("{$state}.blur.title", 'Deferred despite blur')
        ->click('#framework-server-state')
        ->assertScript("{$state}.blur.title", 'Now live on blur')
        ->assertScript('document.documentElement.classList.contains("dark")', $dark)
        ->assertNoSmoke();
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('binds tab and step methods to their own instances and preserves wizard autofocus', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?scenario=panels&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $page->assertValue('[id="form.deferred"]', 'Original café');
    foreach (['tab' => 'Tab', 'step' => 'Step'] as $kind => $label) {
        foreach (['first' => 'First', 'second' => 'Second'] as $position => $name) {
            $selector = ($kind === 'tab' ? '.fi-sc-tabs-tab' : '.fi-sc-wizard-step') . '[data-method-' . $kind . '=' . $position . ']';
            $page->script("async () => { const element = document.querySelector('{$selector}'); element.dataset.report = await Alpine.\$data(element).\$scopeLabel(); }");
            $page->assertAttribute($selector, 'data-report', $label . ': ' . $name . ' ' . $kind);
            $page->script("async () => { const element = document.querySelector('{$selector}'); element.dataset.report = await Alpine.\$data(element).\$callSchemaComponentMethod('scopeLabel'); }");
            $page->assertAttribute($selector, 'data-report', $label . ': ' . $name . ' ' . $kind);
        }
    }
    $page->click('Second tab')->assertValue('[id="form.live"]', 'Original café')
        ->fill('[id="form.live"]', 'Tab edit')->click('Sync')
        ->assertScript("JSON.parse(document.querySelector('#framework-server-state').textContent).live.title", 'Tab edit')
        ->fill('[id="form.first_focus"]', 'Ready')
        ->click('Next')
        ->assertScript('document.activeElement.id', 'form.second_focus')
        ->click('Back')
        ->assertScript('document.activeElement.id', 'form.first_focus')
        ->keys('[id="form.first_focus"]', 'Enter')
        ->assertScript('document.activeElement.id', 'form.second_focus')
        ->assertScript("'incorrect_step_call' in JSON.parse(document.querySelector('#framework-server-state').textContent)", false)
        ->assertNoSmoke();
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('honors explicit and inherited `stateBindingModifiers()`', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?scenario=modifiers&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertValue('[id="form.live"]', 'Original café');
    $page->script('() => { window.requests = 0; Livewire.hook("request", () => window.requests++); }');
    $page->fill('[id="form.live"]', 'Explicitly deferred')->click('#framework-server-state')->wait(0.8)
        ->assertScript('window.requests', 0)
        ->assertScript("{$state}.live.title", 'Original café')
        ->fill('[id="form.deferred"]', 'Inherited live')
        ->assertScript("{$state}.deferred.title", 'Inherited live')
        ->assertScript("{$state}.live.title", 'Explicitly deferred');
    $page->script('window.requests = 0');
    $page->fill('[id="form.blur"]', 'Explicit blur')->wait(0.8)
        ->assertScript('window.requests', 0)
        ->click('#framework-server-state')
        ->assertScript("{$state}.blur.title", 'Explicit blur');
    $page->script('window.requests = 0');
    $page->fill('[id="form.debounce"]', 'Explicit debounce')->wait(0.2)
        ->assertScript('window.requests', 0)
        ->assertScript("{$state}.debounce.title", 'Original café')
        ->assertScript("{$state}.debounce.title", 'Explicit debounce')
        ->assertScript('window.requests', 1)
        ->assertNoSmoke();
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);

it('keeps inline-label renderers full width with descriptions in both wrappers', function (string $framework, bool $dark, string $wrapper): void {
    $page = visit('/js-field-framework-test?scenario=inline-' . $wrapper . '&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $fullWidth = "Math.abs(document.querySelector('[data-field=live]').getBoundingClientRect().width - document.getElementById('form.live-description').parentElement.getBoundingClientRect().width) < 1";
    $page->assertValue('[id="form.live"]', 'Original café')
        ->assertScript($fullWidth, true)
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertScript($fullWidth, true)
        ->assertNoSmoke()->assertNoAccessibilityIssues();
    $page->screenshotElement('.fi-fo-field:has([id="form.live"])', 'js-field-inline-' . $framework . '-' . $wrapper . ($dark ? '-dark' : '-light'));
})->with('JS field frameworks')->with(['light' => false, 'dark' => true])->with(['embedded', 'blade']);

it('scopes utilities to components in lists, tables and liberated layouts', function (string $framework, bool $dark): void {
    $page = visit('/js-field-framework-test?layout=1&framework=' . $framework);
    if ($dark) {
        $page = $page->inDarkMode();
    }
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->click('[data-list-select] .fi-select-input-btn')
        ->fill('[data-list-select] .fi-select-input-search-ctn input', 'Café')
        ->click('[data-list-select] .fi-select-input-option:has-text("Match Café")')
        ->assertScript("{$state}.choice", 'chosen');
    foreach (['list', 'liberated'] as $name) {
        $report = "JSON.parse(document.querySelector('[data-field={$name}] [data-report]').textContent || 'null')";
        $page->click('[data-field=' . $name . '] button:has-text("Call field method")')
            ->assertScript($report, ['path' => 'data.' . $name, 'previous' => 'Original café', 'title' => 'PHP café replacement'])
            ->click('[data-field=' . $name . '] button:has-text("Call renderless method")')
            ->assertScript($report, 'Read: PHP café replacement');
    }
    $page->assertScript("getComputedStyle(document.querySelector('.fi-sc-liberated')).display", 'contents')
        ->assertScript("getComputedStyle(document.querySelector('[data-liberated-hidden]')).display", 'none')
        ->assertScript("getComputedStyle(document.querySelector('[data-liberated-width]')).maxWidth", '448px')
        ->assertScript("getComputedStyle(document.querySelector('[data-liberated-grow]')).flexGrow", '1')
        ->click('[data-field=table] button:has-text("Call field method") >> nth=1')
        ->assertScript("JSON.parse(document.querySelectorAll('[data-field=table] [data-report]')[1].textContent || 'null')", ['path' => 'data.items.1.field', 'previous' => 'Second row', 'title' => 'PHP café replacement'])
        ->click('[data-field=table] button:has-text("Call renderless method") >> nth=0')
        ->assertScript("JSON.parse(document.querySelectorAll('[data-field=table] [data-report]')[0].textContent || 'null')", 'Read: Original café')
        ->click('[data-blade-method=caption] button:has-text("Named Blade call") >> nth=1')
        ->assertSeeIn('[data-blade-method=caption] output >> nth=1', 'data.items.1.caption: Second caption: Named café')
        ->click('[data-blade-method=caption] button:has-text("General Blade call") >> nth=0')
        ->assertSeeIn('[data-blade-method=caption] output >> nth=0', 'data.items.0.caption: First caption: General café')
        ->click('Remove first')
        ->click('[data-field=table] button:has-text("Call renderless method")')
        ->assertScript("JSON.parse(document.querySelector('[data-field=table] [data-report]').textContent || 'null')", 'Read: PHP café replacement')
        ->click('[data-blade-method=caption] button:has-text("General Blade call")')
        ->assertSeeIn('[data-blade-method=caption] output', 'data.items.1.caption: Second caption: General café')
        ->assertScript('document.documentElement.classList.contains("dark")', $dark)
        ->assertNoSmoke();
    $rootContent = '.fi-fo-field:has([data-field=liberated]) [data-embedded-scope]';
    $rowContent = 'td:has([data-field=table]) [data-embedded-scope]';
    $page->assertSeeIn($rootContent . ' span', 'Root "caption" <em>literal</em>')
        ->assertSeeIn($rowContent . ' span', 'Second caption')
        ->click($rootContent . ' button')
        ->assertScript("{$state}.caption", 'Embedded café')
        ->assertScript("{$state}.items[1].caption", 'Second caption')
        ->click($rowContent . ' button')
        ->assertScript("{$state}.items[1].caption", 'Embedded café')
        ->assertScript("{$state}.caption", 'Embedded café')
        ->assertNoSmoke();
    $page->script('window.scrollTo(0, 0)');
    $page->screenshot(filename: 'js-field-layouts-' . $framework . ($dark ? '-dark' : '-light'));
})->with('JS field frameworks')->with(['light' => false, 'dark' => true]);
