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
    foreach (['js-field-renderers/react.js', 'js-renderer-starters/field-react.js'] as $entry) {
        if (! is_file(dirname(__DIR__, 4) . '/build/' . $entry)) {
            throw new LogicException('Run `npm run test:js` before the framework browser tests.');
        }
    }
});

beforeEach(function (): void {
    foreach (['js-field-renderers' => 'tests/js-fields', 'js-renderer-starters' => 'tests/js-field-starters'] as $directory => $package) {
        foreach (File::allFiles(dirname(__DIR__, 4) . '/build/' . $directory) as $file) {
            $id = str_replace('\\', '/', $file->getRelativePathname());
            $id = substr($id, 0, -(strlen($file->getExtension()) + 1));
            FilamentAsset::register([
                $file->getExtension() === 'css'
                    ? Css::make($id, $file->getPathname())
                    : Js::make($id, $file->getPathname())->loadedOnRequest(),
            ], $package);
        }
    }
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('binds generated field templates and preserves callbacks after updates', function (string $framework): void {
    $url = '/js-field-framework-test?scenario=generated&framework=' . $framework;
    $browser = visit($url);
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";

    $browser->assertValue('[id="form.live"]', 'Original café')
        ->assertNoAccessibilityIssues()
        ->fill('[id="form.live"]', 'Edited café')
        ->assertScript("{$state}.live", 'Edited café')
        ->click('Reset')
        ->assertValue('[id="form.live"]', 'Original café')
        ->fill('[id="form.live"]', 'After reset')
        ->assertScript("{$state}.live", 'After reset')
        ->fill('[id="form.blur"]', 'After blur')
        ->assertScript("{$state}.blur", 'Second field');

    $browser->script('document.getElementById("form.blur").blur()');
    $browser->assertScript("{$state}.blur", 'After blur')
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertNoAccessibilityIssues()
        ->click('Toggle locked')
        ->assertDisabled('[id="form.live"]')
        ->assertAttribute('[id="form.blur"]', 'readonly', '')
        ->click('Toggle locked')
        ->click('Toggle mounted')
        ->assertNotPresent('[id="form.live"]')
        ->click('Toggle mounted')
        ->assertValue('[id="form.live"]', 'After reset')
        ->fill('[id="form.live"]', 'After remount')
        ->assertScript("{$state}.live", 'After remount')
        ->assertNoSmoke();

    if (! in_array($framework, ['js', 'js-ts'])) {
        $browser->assertScript("performance.getEntriesByType('resource').some(entry => new URL(entry.name).pathname.startsWith('/js/tests/js-field-starters/chunks/'))", true);
    }

    visit($url)->inDarkMode()
        ->assertNoAccessibilityIssues()
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertNoAccessibilityIssues();
})->with(['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts']);

it('calls exposed field methods and the owning `$wire` across nested instances and remounts', function (string $framework): void {
    $page = visit('/js-field-framework-test?framework=' . $framework);
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
        ->assertNoSmoke();
})->with('JS field frameworks');

it('synchronizes composite state with deferred, live, blur and debounce bindings', function (string $framework): void {
    $page = visit('/js-field-framework-test?framework=' . $framework);
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertValue('[id="form.deferred"]', 'Original café');
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
    $page->wait(0.7)->assertScript('window.requests', $requests)->assertNoSmoke();
})->with('JS field frameworks');

it('contains initialization failures without losing state or breaking other framework instances', function (string $framework): void {
    $url = '/js-field-framework-test?scenario=failure&framework=' . $framework;
    $page = visit($url);
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertVisible('[data-field="unavailable"] [role="alert"]')
        ->assertScript("{$state}.unavailable", ['title' => 'Original café', 'enabled' => true, 'tags' => ['email']])
        ->fill('[id="form.live"]', 'Still working')
        ->assertScript("{$state}.live.title", 'Still working')
        ->assertScript("{$state}.unavailable", ['title' => 'Original café', 'enabled' => true, 'tags' => ['email']]);
    $page->script('document.querySelector("[data-field=unavailable]").scrollIntoView({ block: "center", behavior: "instant" })');
    $page->assertVisible('[data-field="unavailable"] [role="alert"]')
        ->assertNoAccessibilityIssues();

    visit($url)->inDarkMode()
        ->assertVisible('[data-field="unavailable"] [role="alert"]')
        ->assertNoAccessibilityIssues();
})->with('JS field frameworks');

it('updates PHP props and nested schema utilities without remounting framework state', function (string $framework): void {
    $page = visit('/js-field-framework-test?framework=' . $framework);
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $configuration = "JSON.parse(document.querySelector('[data-field=deferred] [data-config]').textContent)";
    $page->assertValue('[id="form.deferred"]', 'Original café')
        ->assertScript('Array.from(document.querySelectorAll("[x-ref=host]")).every(host => host.querySelector("input"))', true);
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
        ->assertNoSmoke();
    $page->click('[data-field="nested"] button:has-text("Use utilities") >> nth=1')
        ->assertScript("{$report}.state.title", 'Nested edit')
        ->assertScript("{$report}.root", 'Changed root by renderer');
})->with('JS field frameworks');

it('lazy loads one module for independent instances and cleans up on removal and remount', function (string $framework): void {
    $page = visit('/js-field-framework-test?unmounted=1&framework=' . $framework);
    $loads = "performance.getEntriesByType('resource').filter(entry => new URL(entry.name).pathname === '/js/tests/js-fields/{$framework}.js').length";
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";
    $page->assertScript($loads, 0)->click('Toggle mounted')
        ->assertValue('[id="form.items.1.field"]', 'Second row')
        ->assertScript($loads, 1);
    $page->assertScript("performance.getEntriesByType('resource').some(entry => new URL(entry.name).pathname.startsWith('/css/tests/js-fields/'))", true);
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
        ->assertNoSmoke();
})->with('JS field frameworks');

it('updates validation and descriptions without replacing or blurring framework inputs', function (string $framework): void {
    $url = '/js-field-framework-test?scenario=description&framework=' . $framework;
    $page = visit($url);
    $page->assertAttribute('[id="form.live"]', 'aria-describedby', 'form.live-description')
        ->assertPresent('[id="form.live-description"]')
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertNoAccessibilityIssues();
    $page->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'false')
        ->assertPresent('[id="form.live-description"]')
        ->assertNoSmoke();

    $page->script('window.fieldInput = document.getElementById("form.live")');
    $page->fill('[id="form.live"]', 'x')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertScript('document.getElementById("form.live") === window.fieldInput', true)
        ->assertScript('document.activeElement === window.fieldInput', true)
        ->fill('[id="form.live"]', 'Valid title')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'false')
        ->assertScript('document.getElementById("form.live") === window.fieldInput', true)
        ->assertScript('document.activeElement === window.fieldInput', true);

    visit($url)->inDarkMode()->assertNoAccessibilityIssues()
        ->fill('[id="form.live"]', 'x')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertNoAccessibilityIssues();
})->with('JS field frameworks');

it('respects a conditional `live(onBlur: true)` binding', function (): void {
    $page = visit('/js-field-framework-test?deferredBlur=1&framework=react');
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
        ->assertNoSmoke();
});

it('binds tab and step methods to their own instances and preserves wizard autofocus', function (): void {
    $page = visit('/js-field-framework-test?scenario=panels&framework=react');
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
});

it('honors explicit and inherited `stateBindingModifiers()`', function (): void {
    $page = visit('/js-field-framework-test?scenario=modifiers&framework=react');
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
});

it('renders inline-label descriptions in both wrappers', function (string $wrapper): void {
    $url = '/js-field-framework-test?scenario=inline-' . $wrapper . '&framework=react';
    $page = visit($url);
    $page->assertValue('[id="form.live"]', 'Original café')
        ->assertAttribute('[id="form.live"]', 'aria-describedby', 'form.live-description')
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertNoSmoke()->assertNoAccessibilityIssues();

    visit($url)->inDarkMode()
        ->click('Show validation error')
        ->assertAttribute('[id="form.live"]', 'aria-invalid', 'true')
        ->assertNoAccessibilityIssues();
})->with(['embedded', 'blade']);

it('keeps exposed methods bound to their fields when `Flex` children are reordered', function (string $framework): void {
    $page = visit('/js-field-framework-test?scenario=flex&framework=' . $framework);
    $state = "JSON.parse(document.querySelector('#framework-server-state').textContent)";

    $page->assertNoAccessibilityIssues()
        ->click('[data-testid=reverse-fields]')
        ->assertScript("Array.from(document.querySelectorAll('[data-blade-method]'), element => element.dataset.bladeMethod)", ['second', 'first'])
        ->assertScript("Array.from(document.querySelectorAll('[data-field]'), element => element.dataset.field)", ['live', 'deferred']);

    foreach (['second', 'first'] as $name) {
        $page->click("[data-blade-method={$name}] button:has-text(\"Named Blade call\")")
            ->assertSeeIn("[data-blade-method={$name}] output", "data.{$name}: Named café")
            ->assertScript("{$state}.{$name}", 'Named café');
    }

    foreach (['live', 'deferred'] as $name) {
        $page->click("[data-field={$name}] button:has-text(\"Call field method\")")
            ->assertScript("JSON.parse(document.querySelector('[data-field={$name}] [data-report]').textContent || 'null')", ['path' => "data.{$name}", 'previous' => 'Original café', 'title' => 'PHP café replacement'])
            ->assertScript("{$state}.{$name}.title", 'PHP café replacement');
    }

    $page->assertNoSmoke()->assertNoAccessibilityIssues();

    visit('/js-field-framework-test?scenario=flex&framework=' . $framework)->inDarkMode()
        ->click('[data-testid=reverse-fields]')
        ->assertScript("Array.from(document.querySelectorAll('[data-field]'), element => element.dataset.field)", ['live', 'deferred'])
        ->assertNoAccessibilityIssues();
})->with('JS field frameworks');

it('scopes utilities to components in lists, tables and liberated layouts', function (): void {
    $page = visit('/js-field-framework-test?layout=1&framework=react');
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
    $page->click('[data-field=table] button:has-text("Call field method") >> nth=1')
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
});
