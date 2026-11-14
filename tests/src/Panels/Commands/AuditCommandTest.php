<?php

use Filament\Auth\Pages\Login;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Auth\Pages\Register;
use Filament\Pages\SimplePage;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Resources\Resource;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

uses(TestCase::class)->group('serial');

$usePanels = function (Panel ...$panels): void {
    $registry = app(PanelRegistry::class);

    $registry->panels = [];
    $registry->defaultPanel = null;

    foreach ($panels as $panel) {
        $registry->panels[$panel->getId()] = $panel;
    }
};

$runAuditAsJson = function (array $parameters = []): array {
    $exitCode = Artisan::call('filament:audit', ['--json' => true, ...$parameters]);

    return [$exitCode, json_decode(Artisan::output(), associative: true)];
};

beforeEach(function (): void {
    Gate::policy(AuditCommandPartialPolicyRecord::class, AuditCommandPartialPolicy::class);
});

it('reports no issues when there are no panels', function () use ($usePanels): void {
    $usePanels();

    $this->artisan('filament:audit')
        ->expectsOutputToContain('No issues found.')
        ->assertSuccessful();
});

it('reports no issues when every resource has a complete policy', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandTicketResource::class]));

    $this->artisan('filament:audit')
        ->expectsOutputToContain('No issues found.')
        ->assertSuccessful();
});

it('outputs an empty JSON array when there are no issues', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandTicketResource::class]));

    [$exitCode, $findings] = $runAuditAsJson();

    expect($exitCode)->toBe(0)
        ->and($findings)->toBe([]);
});

it('warns about a resource whose model has no policy', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    [$exitCode, $findings] = $runAuditAsJson();

    expect($exitCode)->toBe(0)
        ->and($findings)->toHaveCount(1)
        ->and($findings[0]['panel'])->toBe('audit')
        ->and($findings[0]['severity'])->toBe('warning')
        ->and($findings[0]['component'])->toBe(AuditCommandPostResource::class)
        ->and($findings[0]['file'])->toEndWith('AuditCommandTest.php')
        ->and($findings[0]['message'])->toBe('No policy was found for [' . Post::class . '], so all operations are allowed. Add a policy or enable strict authorization.');
});

it('warns about a resource whose policy is missing methods', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPartialPolicyResource::class]));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['severity'])->toBe('warning')
        ->and($findings[0]['component'])->toBe(AuditCommandPartialPolicyResource::class)
        ->and($findings[0]['message'])->toBe('The [' . AuditCommandPartialPolicy::class . '] policy is missing [create(), update(), delete(), deleteAny()], so those operations are allowed. Add the methods or enable strict authorization.');
});

it('reports resources that skip authorization as info', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandSkippedAuthorizationResource::class]));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['severity'])->toBe('info')
        ->and($findings[0]['component'])->toBe(AuditCommandSkippedAuthorizationResource::class)
        ->and($findings[0]['message'])->toBe('Authorization is skipped, so all panel users can perform every operation.');
});

it('reports resources that skip authorization even when authorization is strict', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->strictAuthorization()->resources([AuditCommandSkippedAuthorizationResource::class]));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['severity'])->toBe('info');
});

it('does not report missing policies when authorization is strict', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->strictAuthorization()->resources([
        AuditCommandPostResource::class,
        AuditCommandPartialPolicyResource::class,
    ]));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toBe([]);
});

it('does not report missing policies when the resource does not check policy existence', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandUncheckedPolicyExistenceResource::class]));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toBe([]);
});

it('reports findings for every panel', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(
        Panel::make()->id('first')->resources([AuditCommandPostResource::class]),
        Panel::make()->id('second')->resources([AuditCommandSkippedAuthorizationResource::class]),
    );

    [, $findings] = $runAuditAsJson();

    expect($findings)->toHaveCount(2)
        ->and(array_column($findings, 'panel'))->toBe(['first', 'second'])
        ->and(array_column($findings, 'component'))->toBe([
            AuditCommandPostResource::class,
            AuditCommandSkippedAuthorizationResource::class,
        ]);
});

it('outputs findings as a table', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    // The panel, severity, and component are printed in the same table row, which is a single
    // console write, so assert against the full captured output instead of per-write expectations.
    $exitCode = Artisan::call('filament:audit');
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain(AuditCommandPostResource::class)
        ->and($output)->toContain('warning')
        ->and($output)->not->toContain('No issues found.');
});

it('does not report the default authentication pages', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(
        Panel::make()
            ->id('audit')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(RequestPasswordReset::class, ResetPassword::class),
    );

    [, $findings] = $runAuditAsJson();

    expect($findings)->toBe([]);
});

it('warns about a login page that does not restrict file uploads', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->login(AuditCommandUnrestrictedGuestPage::class));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['panel'])->toBe('audit')
        ->and($findings[0]['severity'])->toBe('warning')
        ->and($findings[0]['component'])->toBe(AuditCommandUnrestrictedGuestPage::class)
        ->and($findings[0]['message'])->toBe('This guest page accepts Livewire file uploads to any property. Add the [' . RestrictsFileUploadsToSchemaComponents::class . '] trait.');
});

it('warns about registration and password reset pages that do not restrict file uploads', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(
        Panel::make()
            ->id('audit')
            ->registration(AuditCommandUnrestrictedGuestPage::class)
            ->passwordReset(AuditCommandUnrestrictedGuestPage::class, AuditCommandUnrestrictedGuestPage::class),
    );

    [, $findings] = $runAuditAsJson();

    expect($findings)->toHaveCount(3)
        ->and(array_unique(array_column($findings, 'component')))->toBe([AuditCommandUnrestrictedGuestPage::class]);
});

it('does not report guest pages that restrict file uploads', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->login(AuditCommandRestrictedGuestPage::class));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toBe([]);
});

it('does not report guest pages that do not interact with schemas', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->login(AuditCommandSchemalessGuestPage::class));

    [, $findings] = $runAuditAsJson();

    expect($findings)->toBe([]);
});

it('ignores guest page actions that are not classes', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(
        Panel::make()
            ->id('audit')
            ->login(fn (): string => 'login')
            ->registration('NonExistentAuditCommandPage'),
    );

    [, $findings] = $runAuditAsJson();

    expect($findings)->toBe([]);
});

it('rejects an invalid `--fail-on` severity', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    $this->artisan('filament:audit', ['--fail-on' => 'critical'])
        ->expectsOutputToContain('The [--fail-on] option must be either [info] or [warning].')
        ->assertExitCode(2);
});

it('fails with `--fail-on=warning` when there is a warning', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    $this->artisan('filament:audit', ['--fail-on' => 'warning'])
        ->assertFailed();
});

it('fails with `--fail-on=info` when there is a warning', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    $this->artisan('filament:audit', ['--fail-on' => 'info'])
        ->assertFailed();
});

it('fails with `--fail-on=info` when there is only an info finding', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandSkippedAuthorizationResource::class]));

    $this->artisan('filament:audit', ['--fail-on' => 'info'])
        ->assertFailed();
});

it('succeeds with `--fail-on=warning` when there is only an info finding', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandSkippedAuthorizationResource::class]));

    $this->artisan('filament:audit', ['--fail-on' => 'warning'])
        ->assertSuccessful();
});

it('succeeds with `--fail-on` when there are no findings', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandTicketResource::class]));

    $this->artisan('filament:audit', ['--fail-on' => 'info'])
        ->assertSuccessful();
});

it('succeeds without `--fail-on` even when there are warnings', function () use ($usePanels): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    $this->artisan('filament:audit')
        ->assertSuccessful();
});

it('can combine `--json` and `--fail-on`', function () use ($usePanels, $runAuditAsJson): void {
    $usePanels(Panel::make()->id('audit')->resources([AuditCommandPostResource::class]));

    [$exitCode, $findings] = $runAuditAsJson(['--fail-on' => 'warning']);

    expect($exitCode)->toBe(1)
        ->and($findings)->toHaveCount(1);
});

class AuditCommandTicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'audit-command-tickets';
}

class AuditCommandPostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'audit-command-posts';
}

class AuditCommandSkippedAuthorizationResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'audit-command-skipped-authorization-posts';

    protected static bool $shouldSkipAuthorization = true;
}

class AuditCommandUncheckedPolicyExistenceResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'audit-command-unchecked-policy-existence-posts';

    protected static bool $shouldCheckPolicyExistence = false;
}

class AuditCommandPartialPolicyRecord extends Model {}

class AuditCommandPartialPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }
}

class AuditCommandPartialPolicyResource extends Resource
{
    protected static ?string $model = AuditCommandPartialPolicyRecord::class;

    protected static ?string $slug = 'audit-command-partial-policy-records';
}

class AuditCommandUnrestrictedGuestPage extends SimplePage {}

class AuditCommandRestrictedGuestPage extends SimplePage
{
    use RestrictsFileUploadsToSchemaComponents;
}

class AuditCommandSchemalessGuestPage extends Component {}
