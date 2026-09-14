<?php

use Filament\Facades\Filament;
use Filament\Http\Middleware\HandleInertiaRequests as PanelInertiaMiddleware;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Inertia\Fixtures\HandleInertiaRequests;
use Filament\Tests\Inertia\Fixtures\PanelUser;
use Filament\Tests\Inertia\Fixtures\RequestState;
use Filament\Tests\Inertia\Fixtures\TestPage;
use Filament\Tests\Inertia\Fixtures\TestSimplePage;
use Filament\Tests\Inertia\TestCase;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Inertia\Inertia;
use Inertia\Middleware;
use Inertia\Ssr\Gateway;
use Inertia\Ssr\Response;
use Livewire\Livewire;

uses(TestCase::class);

dataset('inertia shells', [
    'Page' => ['/integration/page', TestPage::class, 'fi-layout'],
    'SimplePage' => ['/integration/simple', TestSimplePage::class, 'fi-simple-layout'],
]);

dataset('inertia request headers', [
    'document' => [[]],
    'full Inertia' => [['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version']],
    'partial Inertia' => [['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version', 'X-Inertia-Partial-Component' => 'Report', 'X-Inertia-Partial-Data' => 'analytics.total']],
]);

it('embeds CSR content in the original `Page` or `SimplePage` shell and evaluates `getInertiaResponse()` once', function (string $path, string $page, string $shell): void {
    $this->get($path)->assertOk()->assertSee($shell, false)
        ->assertSee('wire:snapshot', false)->assertSee('data-inertia-container', false)
        ->assertSee('data-inertia-content inert', false)->assertSee('Loading page…')
        ->assertSee('fixture-renderer.js', false)->assertHeaderMissing('X-Inertia');

    expect(RequestState::$responses)->toBe(1)->and(RequestState::$mounts)->toBe(1);
})->with('inertia shells');

it('returns native Inertia JSON without mounting or rendering either shell', function (string $path): void {
    $this->get($path . '?value=fresh', ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version', 'X-Test-Label' => 'request-shared'])
        ->assertOk()->assertHeader('X-Inertia', 'true')->assertJsonPath('component', 'Report')
        ->assertJsonPath('props.value', 'fresh')->assertJsonPath('props.sharedLabel', 'request-shared')
        ->assertJsonMissingPath('props.optional')->assertDontSee('wire:snapshot', false);

    expect(RequestState::$responses)->toBe(1)->and(RequestState::$mounts)->toBe(0);
})->with('inertia shells');

it('denies access before evaluating props for document, full and partial Inertia requests', function (string $path, string $page, string $shell, array $headers): void {
    RequestState::$allowed = false;
    $this->get($path, $headers)->assertForbidden();
    expect(RequestState::$responses)->toBe(0)->and(RequestState::$mounts)->toBe(0);
})->with('inertia shells')->with('inertia request headers');

it('retains route authentication before props for document, full and partial Inertia requests', function (string $path, string $page, string $shell, array $headers): void {
    auth()->logout();
    $this->get($path, $headers)->assertRedirect();
    expect(RequestState::$responses)->toBe(0)->and(RequestState::$shares)->toBe(0);
})->with('inertia shells')->with('inertia request headers');

it('does not rebuild props on shell updates and reauthorizes after access revocation', function (string $path, string $page): void {
    $component = Livewire::test($page);
    expect(RequestState::$responses)->toBe(1);
    $component->call('refreshShell')->assertSet('shellRefreshes', 1);
    expect(RequestState::$responses)->toBe(1);
    RequestState::$allowed = false;
    $component->call('refreshShell')->assertForbidden();
    expect(RequestState::$responses)->toBe(1);
})->with('inertia shells');

it('calls the SSR gateway `dispatch()` once and leaves the rendered root inert until the host mounts', function (string $path): void {
    config(['inertia.ssr.enabled' => true]);
    $this->mock(Gateway::class)->shouldReceive('dispatch')->once()
        ->andReturn(new Response('', '<div id="filament-inertia" data-server-rendered="true"><h2>Server report</h2></div>'));

    $response = $this->get($path)->assertOk()->assertSee('Server report')->assertSee('data-inertia-content inert', false);
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    expect((new DOMXPath($document))->evaluate('boolean(//*[@data-inertia-loading][@hidden])'))->toBeTrue();
    expect(RequestState::$responses)->toBe(1);
})->with('inertia shells');

it('preserves nested partial filtering and optional evaluation', function (string $path): void {
    $this->get($path, ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version', 'X-Inertia-Partial-Component' => 'Report', 'X-Inertia-Partial-Data' => 'analytics.total,optional'])
        ->assertOk()->assertJsonPath('props.analytics.total', 137)->assertJsonPath('props.optional', 'selected')
        ->assertJsonMissingPath('props.analytics.audit')->assertJsonMissingPath('props.value');
})->with('inertia shells');

it('does not reuse response props or middleware shared data across requests', function (): void {
    $headers = ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version'];
    $this->get('/integration/page?value=first', [...$headers, 'X-Test-Label' => 'page'])
        ->assertJsonPath('props.value', 'first')->assertJsonPath('props.sharedLabel', 'page');
    $this->get('/integration/simple?value=second', [...$headers, 'X-Test-Label' => 'simple'])
        ->assertJsonPath('props.value', 'second')->assertJsonPath('props.sharedLabel', 'simple');
    expect(RequestState::$responses)->toBe(2)->and(RequestState::$mounts)->toBe(0);
});

it('retains native version conflict responses', function (): void {
    $this->get('/integration/page', ['X-Inertia' => 'true', 'X-Inertia-Version' => 'old'])
        ->assertStatus(409)->assertHeader('X-Inertia-Location', url('/integration/page'));
});

it('does not add Inertia middleware or require a renderer with the adapter installed but the plugin disabled', function (): void {
    $panel = Filament::getPanel('admin');
    expect(array_filter($panel->getMiddleware(), static fn (string $middleware): bool => str_starts_with($middleware, PanelInertiaMiddleware::class)))->toBe([]);
    Filament::setCurrentPanel($panel);
    $this->get(Settings::getUrl())->assertOk()->assertDontSee('data-inertia-container', false);
    expect(RequestState::$responses)->toBe(0);
});

it('rejects resource pages before the fast path can bypass denied mount authorization', function (bool $inertia): void {
    $this->withoutExceptionHandling();
    expect(fn () => $this->get('/integration/resource', $inertia ? ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version'] : []))
        ->toThrow(LogicException::class, 'Resource pages depend on the Livewire record lifecycle.');
    expect(RequestState::$responses)->toBe(0);
})->with([false, true]);

it('starts the session before sharing props and forwards middleware termination', function (): void {
    $this->get('/integration/page', ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version'])
        ->assertOk()->assertJsonPath('props.sessionAvailable', true);
    expect(RequestState::$terminations)->toBe(1);
});

it('keeps validation errors and 303 redirects from the configured middleware', function (): void {
    $headers = ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version'];
    $this->from('/integration/page')->patch('/integration/draft', ['title' => 'No'], $headers)
        ->assertStatus(303)->assertSessionHasErrors('title');
    $this->get('/integration/page', $headers)->assertOk()->assertJsonStructure(['props' => ['errors' => ['title']]]);
    $this->patch('/integration/draft', ['title' => 'Valid title'], $headers)->assertStatus(303)->assertRedirect('/integration/page');
});

it('prioritizes only the panel wrapper and leaves outside native middleware ordering unchanged', function (): void {
    $priority = app(Kernel::class)->getMiddlewarePriority();
    expect($priority)->toContain(PanelInertiaMiddleware::class)->not->toContain(Middleware::class, HandleInertiaRequests::class);
    expect(array_search(PanelInertiaMiddleware::class, $priority))->toBeGreaterThan(array_search(ShareErrorsFromSession::class, $priority));

    Route::get('/outside-inertia', static fn (Request $request) => Inertia::render('Outside', []))
        ->middleware([HandleInertiaRequests::class, StartSession::class, ShareErrorsFromSession::class]);
    $this->get('/outside-inertia', ['X-Inertia' => 'true', 'X-Inertia-Version' => 'fixture-version'])
        ->assertOk()->assertJsonPath('props.sessionAvailable', false);
});

it('shares eager props using the panel guard and only the identified tenant records', function (array $headers): void {
    $firstTenant = Team::factory()->create();
    $secondTenant = Team::factory()->create();
    $panelUser = PanelUser::query()->findOrFail(User::factory()->create(['email' => 'panel@example.com'])->getKey());
    $firstMember = User::factory()->create(['email' => 'first-tenant@example.com']);
    $secondMember = User::factory()->create(['email' => 'second-tenant@example.com']);
    $firstTenant->users()->attach([$panelUser->getKey(), $firstMember->getKey()]);
    $secondTenant->users()->attach([$panelUser->getKey(), $secondMember->getKey()]);
    auth()->guard('panel')->setUser($panelUser);
    $defaultUser = auth()->guard('web')->user();
    expect($defaultUser->getAuthIdentifier())->not->toBe($panelUser->getAuthIdentifier());

    if (isset($headers['X-Inertia-Partial-Data'])) {
        $headers['X-Inertia-Partial-Data'] = 'sharedUser,sharedTenant,sharedRecords';
    }

    foreach ([[$firstTenant, $firstMember, $secondMember], [$secondTenant, $secondMember, $firstMember]] as [$tenant, $member, $otherMember]) {
        Filament::setTenant(null);
        auth()->shouldUse('web');
        $response = $this->get('/integration-tenancy/' . $tenant->getKey() . '/page', $headers)->assertOk();

        if (isset($headers['X-Inertia'])) {
            $props = $response->json('props');
        } else {
            $document = new DOMDocument;
            @$document->loadHTML($response->getContent());
            $pageData = (new DOMXPath($document))->query('//script[@data-page="filament-inertia"]')->item(0);
            $props = json_decode($pageData->textContent, true, flags: JSON_THROW_ON_ERROR)['props'];
        }

        expect($props['sharedUser'])->toBe($panelUser->getKey())
            ->and($props['sharedTenant'])->toBe($tenant->getKey())
            ->and($props['sharedRecords'])->toBe([$panelUser->email, $member->email])
            ->not->toContain($otherMember->email, $defaultUser->email);
    }

    expect(RequestState::$shares)->toBe(2);
})->with('inertia request headers');

it('never shares eager tenant records when panel authentication or tenant access fails', function (array $headers, bool $authenticated): void {
    $tenant = Team::factory()->create();

    if ($authenticated) {
        auth()->guard('panel')->setUser(PanelUser::query()->findOrFail(User::factory()->create()->getKey()));
    }

    auth()->shouldUse('web');
    $response = $this->get('/integration-tenancy/' . $tenant->getKey() . '/page', $headers);
    $authenticated ? $response->assertNotFound() : $response->assertRedirect();

    expect(RequestState::$shares)->toBe(0)->and(RequestState::$responses)->toBe(0);
})->with('inertia request headers')->with([false, true]);
