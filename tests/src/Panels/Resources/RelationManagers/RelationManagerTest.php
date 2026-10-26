<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Enums\ContentTabPosition;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Resources\Departments\DepartmentResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicketWithContentTabs;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\ContentTabsDepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManagerWithTabs;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachActionRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachTableSelectAndModifiedQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachTableSelectRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithDeferredBadgeRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithMixedSummaryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithModifiedQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithPivotAndModifiedQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithPivotSummaryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithSubquerySelectAndDetachRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\ScopedDepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithAssociateActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(TestCase::class);

describe('rendering and authorization', function (): void {
    it('can render relation manager', function (): void {
        $ticket = Ticket::factory()
            ->create();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->assertSuccessful();
    });

    it('can list departments', function (): void {
        $ticket = Ticket::factory()
            ->hasAttached(Department::factory(10))
            ->create();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->assertCanSeeTableRecords($ticket->departments);
    });

    it('resolves record URLs and actions without leaking record context between records', function (bool $isTableLoadingDeferred, string $actionHierarchy): void {
        $ticket = Ticket::factory()->create();
        $departments = collect([
            Department::factory()->create(['name' => 'View URL']),
            Department::factory()->create(['name' => 'Edit URL']),
            Department::factory()->create(['name' => 'Edit action']),
            Department::factory()->create(['name' => 'No access']),
        ]);

        $ticket->departments()->attach($departments);

        RecordUrlResolutionRelationManager::$initialActionRecordKeys = [];

        $component = livewire(RecordUrlResolutionRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'areActionsGrouped' => $actionHierarchy === 'grouped',
            'areActionsNested' => $actionHierarchy === 'nested',
            'hasRecordDependentRootGroupTriggerView' => $actionHierarchy !== 'ungrouped',
            'isTableLoadingDeferred' => $isTableLoadingDeferred,
        ])->assertSuccessful();

        expect(RecordUrlResolutionRelationManager::$initialActionRecordKeys)->toBe([null, null]);

        if ($isTableLoadingDeferred) {
            $component
                ->assertDontSeeHtml("href=\"/departments/{$departments[0]->getKey()}/view\"")
                ->assertDontSeeHtml("href=\"/departments/{$departments[1]->getKey()}/edit\"")
                ->call('loadTable');
        }

        $component
            ->assertSeeHtml("href=\"/departments/{$departments[0]->getKey()}/view\"")
            ->assertSeeHtml("href=\"/departments/{$departments[1]->getKey()}/edit\"")
            ->assertSeeHtml('target="_blank"');

        $table = $component->instance()->getTable();
        $viewAction = $table->getAction('view');
        $editAction = $table->getAction('edit');

        $assertActionHierarchyIsUnbound = function (Action $action): void {
            expect($action->getRecord(withDefault: false))->toBeNull();

            $actionGroup = $action->getGroup();

            while ($actionGroup) {
                expect($actionGroup->getRecord(withDefault: false))->toBeNull();

                $actionGroup = $actionGroup->getGroup();
            }
        };

        $assertActionHierarchyIsUnbound($viewAction);
        $assertActionHierarchyIsUnbound($editAction);

        $assertResolution = function (Department $department, ?string $expectedUrl, ?string $expectedAction, bool $shouldOpenInNewTab) use ($assertActionHierarchyIsUnbound, $editAction, $table, $viewAction): void {
            expect($table->getRecordAction($department))->toBe($expectedAction)
                ->and($table->getRecordUrl($department))->toBe($expectedUrl)
                ->and($table->shouldOpenRecordUrlInNewTab($department))->toBe($shouldOpenInNewTab);

            $assertActionHierarchyIsUnbound($viewAction);
            $assertActionHierarchyIsUnbound($editAction);
        };

        $assertResolution($departments[0], "/departments/{$departments[0]->getKey()}/view", null, true);
        $assertResolution($departments[1], "/departments/{$departments[1]->getKey()}/edit", null, false);
        $assertResolution($departments[2], null, 'edit', false);
        $assertResolution($departments[3], null, null, false);

        $assertResolution($departments[3], null, null, false);
        $assertResolution($departments[2], null, 'edit', false);
        $assertResolution($departments[1], "/departments/{$departments[1]->getKey()}/edit", null, false);
        $assertResolution($departments[0], "/departments/{$departments[0]->getKey()}/view", null, true);

        $component
            ->call('$refresh')
            ->assertSeeHtml("href=\"/departments/{$departments[0]->getKey()}/view\"")
            ->assertSeeHtml("href=\"/departments/{$departments[1]->getKey()}/edit\"");

        $refreshedTable = $component->instance()->getTable();

        $assertActionHierarchyIsUnbound($refreshedTable->getAction('view'));
        $assertActionHierarchyIsUnbound($refreshedTable->getAction('edit'));
    })->with([
        'eager table loading' => false,
        'deferred table loading' => true,
    ])->with([
        'ungrouped actions' => 'ungrouped',
        'grouped actions' => 'grouped',
        'nested actions' => 'nested',
    ]);

    it('removes replaced related resource `recordActions()` from lookup and record navigation', function (): void {
        $ownerRecord = User::factory()->create();
        $record = Post::factory()->for($ownerRecord, 'author')->create();

        $component = livewire(RelatedResourcePostsRelationManager::class, ['ownerRecord' => $ownerRecord, 'pageClass' => EditUser::class]);
        $table = $component->instance()->getTable();

        expect($table->getAction('edit'))->toBeNull()
            ->and($table->getAction('delete'))->toBeNull()
            ->and($table->getAction('associate'))->toBeInstanceOf(AssociateAction::class)
            ->and($table->getRecordUrl($record))->toBeNull()
            ->and($table->getRecordAction($record))->toBeNull();

        $component
            ->mountAction(TestAction::make(EditAction::class)->table($record))
            ->assertActionNotMounted();
    });

    it('does not leak grouped action context from a previous record while resolving record URLs', function (): void {
        $ticket = Ticket::factory()->create();
        $editUrlDepartment = Department::factory()->create(['name' => 'Edit URL']);
        $viewUrlDepartment = Department::factory()->create(['name' => 'View URL']);

        $ticket->departments()->attach([$editUrlDepartment, $viewUrlDepartment]);

        $component = livewire(RecordUrlResolutionRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'areActionsNested' => true,
            'hasCustomRecordAction' => true,
        ])->assertSuccessful();

        $document = new DOMDocument;
        @$document->loadHTML($component->html());

        $xpath = new DOMXPath($document);
        $editUrlRow = $xpath->query("//tr[contains(@class, 'fi-ta-row')][contains(., 'Edit URL')]")->item(0);
        $viewUrlRow = $xpath->query("//tr[contains(@class, 'fi-ta-row')][contains(., 'View URL')]")->item(0);

        expect($editUrlRow)->not->toBeNull()
            ->and($viewUrlRow)->not->toBeNull();

        $editUrlRowHtml = $document->saveHTML($editUrlRow);
        $viewUrlRowHtml = $document->saveHTML($viewUrlRow);

        expect($editUrlRowHtml)->toContain("href=\"/departments/{$editUrlDepartment->getKey()}/edit\"")
            ->and($viewUrlRowHtml)->toContain("href=\"/departments/{$viewUrlDepartment->getKey()}/view\"")
            ->and($viewUrlRowHtml)->not->toContain("href=\"/departments/{$editUrlDepartment->getKey()}/edit\"");
    });

    it('does not leak grouped action context from a previous record while resolving record actions', function (): void {
        $ticket = Ticket::factory()->create();
        $editUrlDepartment = Department::factory()->create(['name' => 'Edit URL']);
        $viewActionDepartment = Department::factory()->create(['name' => 'View action']);

        $ticket->departments()->attach([$editUrlDepartment, $viewActionDepartment]);

        $component = livewire(RecordUrlResolutionRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'areActionsGrouped' => true,
        ])->assertSuccessful();

        $document = new DOMDocument;
        @$document->loadHTML($component->html());

        $xpath = new DOMXPath($document);
        $viewActionRow = $xpath->query("//tr[contains(@class, 'fi-ta-row')][contains(., 'View action')]")->item(0);

        expect($viewActionRow)->not->toBeNull();

        $viewActionRowHtml = $document->saveHTML($viewActionRow);

        expect($viewActionRowHtml)->not->toContain("href=\"/departments/{$editUrlDepartment->getKey()}/edit\"");
    });

    it('respects hidden intermediate action groups while resolving record URLs and actions', function (): void {
        $ticket = Ticket::factory()->create();
        $editUrlDepartment = Department::factory()->create(['name' => 'Edit URL']);
        $editActionDepartment = Department::factory()->create(['name' => 'Edit action']);

        $ticket->departments()->attach([$editUrlDepartment, $editActionDepartment]);

        $component = livewire(RecordUrlResolutionRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'areActionsNested' => true,
            'isNestedGroupHidden' => true,
        ])->assertSuccessful();

        $table = $component->instance()->getTable();

        expect($table->getRecordUrl($editUrlDepartment))->toBeNull()
            ->and($table->getRecordAction($editActionDepartment))->toBeNull();
    });

    it('evaluates intermediate action group visibility against each current record', function (): void {
        $ticket = Ticket::factory()->create();
        $editUrlDepartment = Department::factory()->create(['name' => 'Edit URL']);
        $editActionDepartment = Department::factory()->create(['name' => 'Edit action']);

        $ticket->departments()->attach([$editUrlDepartment, $editActionDepartment]);

        $table = livewire(RecordUrlResolutionRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'areActionsNested' => true,
            'hasRecordDependentNestedGroupVisibility' => true,
        ])->instance()->getTable();

        expect($table->getRecordUrl($editUrlDepartment))->toBe("/departments/{$editUrlDepartment->getKey()}/edit")
            ->and($table->getRecordAction($editActionDepartment))->toBeNull()
            ->and($table->getRecordAction($editUrlDepartment))->toBeNull()
            ->and($table->getRecordUrl($editActionDepartment))->toBeNull();
    });

    it('can render relation manager if the policy `viewAny()` returns `true`', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeTrue();
    });

    it('can render relation manager if the policy `viewAny()` returns an allowed response', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): Response => Response::allow());

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeTrue();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    it('does not render relation manager if the policy `viewAny()` returns `false`', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => false);

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeFalse();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    it('does not render relation manager if the policy `viewAny()` returns a denied response', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): Response => Response::deny());

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeFalse();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    describe('lifecycle authorization', function (): void {
        beforeEach(function (): void {
            Filament::setCurrentPanel(Filament::getPanel('admin'));
            AuthorizationRelationManager::$calls = [];
            AuthorizationRelationManager::$authorizationArguments = [];
            RelationManagerAuthorizationPage::$grouped = false;
        });

        it('authorizes the owner record and page before first mounting a relation manager', function (bool $canView): void {
            $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Private department']))->create();
            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => $canView);

            $component = livewire(AuthorizationRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class]);

            if ($canView) {
                $component->assertSuccessful()->assertSee('Private department');
            } else {
                $component->assertDontSee('Private department')->assertForbidden();
            }

            expect(AuthorizationRelationManager::$calls)->toBe($canView
                ? ['boot', 'authorize', 'mount', 'table', 'render']
                : ['boot', 'authorize'])
                ->and(AuthorizationRelationManager::$authorizationArguments)->toBe([
                    [$ticket->getKey(), EditTicket::class],
                ]);
        })->with([true, false]);

        it('uses the relation manager class for authorization when `$pageClass` is omitted', function (): void {
            $ticket = Ticket::factory()->create();
            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => false);

            livewire(AuthorizationRelationManager::class, ['ownerRecord' => $ticket])->assertForbidden();

            expect(AuthorizationRelationManager::$calls)->toBe(['boot', 'authorize'])
                ->and(AuthorizationRelationManager::$authorizationArguments)->toBe([[$ticket->getKey(), AuthorizationRelationManager::class]]);
        });

        it('authorizes the real lazy relation manager mount after its placeholder', function (bool $canView): void {
            $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Private department']))->create();

            $component = livewire(AuthorizationRelationManager::class, [
                'ownerRecord' => $ticket,
                'pageClass' => EditTicket::class,
                'lazy' => true,
            ])->assertSuccessful()->assertDontSee('Private department');

            expect(AuthorizationRelationManager::$calls)->toBe([]);

            preg_match('/__lazyLoad\(\'([^\']+)\'\)/', html_entity_decode($component->html()), $matches);

            expect($matches)->toHaveKey(1);

            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => $canView);

            $component->call('__lazyLoad', $matches[1]);

            if ($canView) {
                $component->assertSuccessful()->assertSee('Private department');
            } else {
                $component->assertDontSee('Private department')->assertForbidden();
            }

            expect(AuthorizationRelationManager::$calls)->toBe($canView
                ? ['authorize', 'boot', 'mount', 'table', 'render']
                : ['authorize'])
                ->and(AuthorizationRelationManager::$authorizationArguments)->toBe([[$ticket->getKey(), EditTicket::class]]);

            if (! $canView) {
                return;
            }

            AuthorizationRelationManager::$calls = [];
            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => false);

            $component->call('sensitiveAction')->assertForbidden()->assertDontSee('Private department');

            expect(AuthorizationRelationManager::$calls)->toBe(['boot', 'authorize']);
        })->with([true, false]);

        it('rejects denied requests before a lazy relation manager has mounted', function (array $calls, array $updates, bool $withLazyLoad): void {
            $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Private department']))->create();
            $component = livewire(AuthorizationRelationManager::class, [
                'ownerRecord' => $ticket,
                'pageClass' => EditTicket::class,
                'lazy' => true,
            ]);

            if ($withLazyLoad) {
                preg_match('/__lazyLoad\(\'([^\']+)\'\)/', html_entity_decode($component->html()), $matches);

                expect($matches)->toHaveKey(1);

                $calls[] = ['method' => '__lazyLoad', 'params' => [$matches[1]]];
            }

            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => false);

            $component->update(calls: $calls, updates: $updates);

            expect(AuthorizationRelationManager::$calls)->toBe(['authorize'])
                ->and(AuthorizationRelationManager::$authorizationArguments)->toBe([[$ticket->getKey(), EditTicket::class]]);

            $component->assertForbidden()->assertDontSee('Private department');
        })->with([
            'action' => [[['method' => 'sensitiveAction', 'params' => []]], [], false],
            'property update' => [[], ['name' => 'changed'], false],
            'empty request' => [[], [], false],
            'property update before lazy mount' => [[], ['name' => 'changed'], true],
            'action before lazy mount' => [[['method' => 'sensitiveAction', 'params' => []]], [], true],
        ]);

        it('re-authorizes a relation manager before subclass `hydrate()`, property updates, and actions', function (bool $canView, bool $updateProperty): void {
            $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Private department']))->create();
            $component = livewire(AuthorizationRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
                ->assertSee('Private department');

            AuthorizationRelationManager::$calls = [];
            AuthorizationRelationManager::$authorizationArguments = [];
            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => $canView);

            if ($updateProperty) {
                $component->set('name', 'changed');
            } else {
                $component->call('sensitiveAction');
            }

            if ($canView) {
                $component->assertSuccessful()->assertSee('Private department');
            } else {
                $component->assertForbidden()->assertDontSee('Private department');
            }

            expect(AuthorizationRelationManager::$calls)->toBe($canView
                ? ['boot', 'authorize', 'hydrate', 'table', ...($updateProperty ? ['updating', 'updated'] : ['action']), 'render']
                : ['boot', 'authorize'])
                ->and(AuthorizationRelationManager::$authorizationArguments)->toBe([[$ticket->getKey(), EditTicket::class]]);
        })->with([true, false])->with([true, false]);

        it('filters denied relation managers before mounting them on resource pages', function (bool $canView, bool $grouped): void {
            $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Private department']))->create();
            app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => $canView);
            RelationManagerAuthorizationPage::$grouped = $grouped;

            $component = livewire(RelationManagerAuthorizationPage::class, ['record' => $ticket->getRouteKey()])
                ->assertSuccessful();

            if ($canView) {
                $component->assertSee('Private department');

                expect(AuthorizationRelationManager::$calls)->toContain('mount', 'table', 'render');
            } else {
                $component->assertDontSee('Private department');

                expect(AuthorizationRelationManager::$calls)->not->toBeEmpty()->each->toBe('authorize');
            }
        })->with([true, false])->with([true, false]);
    });

    it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false, bool $isBulkAction = false): void {
        app()->bind(DepartmentPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

        $ticket = Ticket::factory()
            ->create();

        $department = Department::factory()
            ->hasAttached($ticket)
            ->create();

        if ($isSoftDeleted) {
            $department->delete();
        }

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->filterTable('trashed', $isSoftDeleted ? 1 : null)
            ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}(TestAction::make($action)->table($department)->bulk($isBulkAction));

        app()->bind(DepartmentPolicy::class . '::' . $policyMethod, fn (): bool => true);
    })->with([
        'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
        'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
        'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
        'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
        'view action with policy returning true' => fn (): array => [ViewAction::class, 'view', true, true, false],
        'view action with policy returning allowed response' => fn (): array => [ViewAction::class, 'view', Response::allow(), true, false],
        'view action with policy returning false' => fn (): array => [ViewAction::class, 'view', false, false, false],
        'view action with policy returning denied response' => fn (): array => [ViewAction::class, 'view', Response::deny(), false, false],
        'edit action with policy returning true' => fn (): array => [EditAction::class, 'update', true, true, false],
        'edit action with policy returning allowed response' => fn (): array => [EditAction::class, 'update', Response::allow(), true, false],
        'edit action with policy returning false' => fn (): array => [EditAction::class, 'update', false, false, false],
        'edit action with policy returning denied response' => fn (): array => [EditAction::class, 'update', Response::deny(), false, false],
        'delete action with policy returning true' => fn (): array => [DeleteAction::class, 'delete', true, true, false],
        'delete action with policy returning allowed response' => fn (): array => [DeleteAction::class, 'delete', Response::allow(), true, false],
        'delete action with policy returning false' => fn (): array => [DeleteAction::class, 'delete', false, false, false],
        'delete action with policy returning denied response' => fn (): array => [DeleteAction::class, 'delete', Response::deny(), false, false],
        'force delete action with policy returning true' => fn (): array => [ForceDeleteAction::class, 'forceDelete', true, true, true],
        'force delete action with policy returning allowed response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::allow(), true, true],
        'force delete action with policy returning false' => fn (): array => [ForceDeleteAction::class, 'forceDelete', false, false, true],
        'force delete action with policy returning denied response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::deny(), false, true],
        'restore action with policy returning true' => fn (): array => [RestoreAction::class, 'restore', true, true, true],
        'restore action with policy returning allowed response' => fn (): array => [RestoreAction::class, 'restore', Response::allow(), true, true],
        'restore action with policy returning false' => fn (): array => [RestoreAction::class, 'restore', false, false, true],
        'restore action with policy returning denied response' => fn (): array => [RestoreAction::class, 'restore', Response::deny(), false, true],
        'replicate action with policy returning true' => fn (): array => [ReplicateAction::class, 'replicate', true, true, false],
        'replicate action with policy returning allowed response' => fn (): array => [ReplicateAction::class, 'replicate', Response::allow(), true, false],
        'replicate action with policy returning false' => fn (): array => [ReplicateAction::class, 'replicate', false, false, false],
        'replicate action with policy returning denied response' => fn (): array => [ReplicateAction::class, 'replicate', Response::deny(), false, false],
        'delete bulk action with policy returning true' => fn (): array => [DeleteBulkAction::class, 'deleteAny', true, true, false, true],
        'delete bulk action with policy returning allowed response' => fn (): array => [DeleteBulkAction::class, 'deleteAny', Response::allow(), true, false, true],
        'delete bulk action with policy returning false' => fn (): array => [DeleteBulkAction::class, 'deleteAny', false, false, false, true],
        'delete bulk action with policy returning denied response' => fn (): array => [DeleteBulkAction::class, 'deleteAny', Response::deny(), false, false, true],
        'force delete bulk action with policy returning true' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', true, true, true, true],
        'force delete bulk action with policy returning allowed response' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', Response::allow(), true, true, true],
        'force delete bulk action with policy returning false' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', false, false, true, true],
        'force delete bulk action with policy returning denied response' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', Response::deny(), false, true, true],
        'restore bulk action with policy returning true' => fn (): array => [RestoreBulkAction::class, 'restoreAny', true, true, true, true],
        'restore bulk action with policy returning allowed response' => fn (): array => [RestoreBulkAction::class, 'restoreAny', Response::allow(), true, true, true],
        'restore bulk action with policy returning false' => fn (): array => [RestoreBulkAction::class, 'restoreAny', false, false, true, true],
        'restore bulk action with policy returning denied response' => fn (): array => [RestoreBulkAction::class, 'restoreAny', Response::deny(), false, true, true]]);

    it('can force render relation manager after create another', function (): void {
        $ticket = Ticket::factory()
            ->create();

        CreateAction::configureUsing(function (CreateAction $action): void {
            $action->forceRenderAfterCreateAnother(fn (mixed $livewire): bool => $livewire instanceof RelationManager);
        });

        $action = TestAction::make(CreateAction::class)->table();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->assertSuccessful()
            ->assertCountTableRecords(0)
            ->assertActionExists($action)
            ->mountAction($action, ['another' => true])
            ->fillForm([
                'name' => $name = Str::random(),
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->assertCountTableRecords(1)
            ->assertSeeText($name);

        assertDatabaseHas(Department::class, ['name' => $name]);
    });
});

describe('record titles and select labels', function (): void {
    it('uses local record title attributes for related resource titles and record select labels', function (string $relationManager, string $actionClass, string $pageClass, string $ownerModel, string $relatedModel, array $attributes, string $titleAttribute, string $title): void {
        $ownerRecord = $ownerModel::factory()->create();
        $record = $relatedModel::factory()->create($attributes);
        $otherRecord = $relatedModel::factory()->create([...$attributes, $titleAttribute => "Another {$title}"]);

        $component = livewire($relationManager, ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass]);

        expect($component->instance()->getTable()->getRecordTitle($record))->toBe($title)
            ->and($component->instance()->getTable()->getRecordTitle($otherRecord))->toBe("Another {$title}");

        $component
            ->mountAction(TestAction::make($actionClass)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($record, $otherRecord, $title): bool {
                expect($select->getOptions())->toEqual([$record->getKey() => $title, $otherRecord->getKey() => "Another {$title}"])
                    ->and($select->getSearchResults('Another'))->toBe([$otherRecord->getKey() => "Another {$title}"])
                    ->and($select->getSearchResults('Unmatched'))->toBe([]);

                $select->state($record->getKey());

                expect($select->getOptionLabel())->toBe($title);

                return true;
            });
    })->with([
        'associate' => [RelatedResourcePostsRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Inherited title', 'content' => 'Contextual title'], 'content', 'Contextual title'],
        'associate with a resource table title' => [TableTitlePostsWithAttributeRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Inherited title', 'content' => 'Contextual title'], 'content', 'Contextual title'],
        'associate with a resource title override' => [CustomTitlePostsWithAttributeRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Inherited title', 'content' => 'Contextual title'], 'content', 'Contextual title'],
        'associate with a static title attribute' => [StaticTitlePostsRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Inherited title', 'content' => 'Contextual title'], 'content', 'Contextual title'],
        'attach' => [RelatedResourceDepartmentsRelationManager::class, AttachAction::class, EditTicket::class, Ticket::class, Department::class, ['name' => 'Contextual department'], 'name', 'Contextual department'],
    ]);

    it('preserves custom resource and legacy `getTableRecordTitle()` titles in record selects', function (string $relationManager, string $actionClass, string $pageClass, string $ownerModel, string $relatedModel, array $attributes, string $title): void {
        $ownerRecord = $ownerModel::factory()->create();
        $record = $relatedModel::factory()->create($attributes);
        $component = livewire($relationManager, ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass]);

        expect($component->instance()->getTable()->getRecordTitle($record))->toBe($title);

        $component
            ->mountAction(TestAction::make($actionClass)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($record, $title): bool {
                expect($select->getOptions())->toBe([$record->getKey() => $title])
                    ->and($select->getSearchResults('Article'))->toBe([$record->getKey() => $title]);

                $select->state($record->getKey());

                expect($select->getOptionLabel())->toBe($title);

                return true;
            });
    })->with([
        'associate resource title' => [CustomTitlePostsRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Article'], 'Resource: Article'],
        'attach resource title' => [CustomTitleDepartmentsRelationManager::class, AttachAction::class, EditTicket::class, Ticket::class, Department::class, ['name' => 'Article department'], 'Resource: Article department'],
        'associate legacy title' => [LegacyTitlePostsRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Article'], 'Legacy: Article'],
        'attach legacy title' => [LegacyTitleDepartmentsRelationManager::class, AttachAction::class, EditTicket::class, Ticket::class, Department::class, ['name' => 'Article department'], 'Legacy: Article department'],
    ]);

    it('loads attribute-based record select labels without retrieving models', function (string $relationManager, string $actionClass, string $pageClass, string $ownerModel, string $relatedModel, array $attributes, string $title): void {
        $ownerRecord = $ownerModel::factory()->create();
        $record = $relatedModel::factory()->create($attributes);
        $component = livewire($relationManager, ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass]);
        $retrievedRecordCount = 0;

        Event::listen("eloquent.retrieved: {$relatedModel}", function () use (&$retrievedRecordCount): void {
            $retrievedRecordCount++;
        });

        $component
            ->mountAction(TestAction::make($actionClass)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($record, $title, &$retrievedRecordCount): bool {
                if ($select->isPreloaded()) {
                    expect($select->getOptions())->toBe([$record->getKey() => $title]);
                }

                expect($select->getSearchResults($title))->toBe([$record->getKey() => $title])
                    ->and($retrievedRecordCount)->toBe(0);

                $select->state($record->getKey());

                expect($select->getOptionLabel())->toBe($title);

                return true;
            });
    })->with([
        'associate' => [AttributeTitlePostsRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Inherited article'], 'Inherited article'],
        'attach' => [AttributeTitleDepartmentsRelationManager::class, AttachAction::class, EditTicket::class, Ticket::class, Department::class, ['name' => 'Inherited department'], 'Inherited department'],
        'associate with an overridden resource title attribute' => [CustomTitlePostsWithAttributeRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Inherited article', 'content' => 'Contextual article'], 'Contextual article'],
        'associate without a related resource' => [PostsWithAssociateActionRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, ['author_id' => null, 'title' => 'Article'], 'Article'],
        'attach without a related resource' => [DepartmentsWithAttachActionRelationManager::class, AttachAction::class, EditTicket::class, Ticket::class, Department::class, ['name' => 'Department'], 'Department'],
    ]);

    it('orders attribute-based record select labels by title unless the query specifies an order', function (string $relationManager, string $actionClass, string $pageClass, string $ownerModel, string $relatedModel, string $titleAttribute, bool $hasCustomOrder): void {
        $ownerRecord = $ownerModel::factory()->create();
        $attributes = $relatedModel === Post::class ? ['author_id' => null] : [];
        $lastRecord = $relatedModel::factory()->create([...$attributes, $titleAttribute => 'Zulu', 'created_at' => '2024-02-01 00:00:00']);
        $firstRecord = $relatedModel::factory()->create([...$attributes, $titleAttribute => 'Alpha', 'created_at' => '2024-01-01 00:00:00']);

        $actionClass::configureUsing(
            static fn (Action $action) => $action->recordSelectOptionsQuery(
                static fn (Builder $query): Builder => $hasCustomOrder ? $query->orderBy($titleAttribute, 'desc') : $query,
            ),
            during: function () use ($relationManager, $actionClass, $pageClass, $ownerRecord, $firstRecord, $lastRecord, $hasCustomOrder): void {
                livewire($relationManager, ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass])
                    ->mountAction(TestAction::make($actionClass)->table())
                    ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($firstRecord, $lastRecord, $hasCustomOrder): bool {
                        expect($select->getOptions())->toBe($hasCustomOrder
                            ? [$lastRecord->getKey() => 'Zulu', $firstRecord->getKey() => 'Alpha']
                            : [$firstRecord->getKey() => 'Alpha', $lastRecord->getKey() => 'Zulu']);

                        return true;
                    });
            },
        );
    })->with([
        'associate' => [AttributeTitlePostsRelationManager::class, AssociateAction::class, EditUser::class, User::class, Post::class, 'title'],
        'attach' => [AttributeTitleDepartmentsRelationManager::class, AttachAction::class, EditTicket::class, Ticket::class, Department::class, 'name'],
    ])->with([false, true]);

    it('formats enum record title attributes for record select labels', function (string $enumClass, string $value, string $title): void {
        $ownerRecord = User::factory()->create();
        $record = Post::factory()->make(['author_id' => null])->mergeCasts(['string_backed_enum' => $enumClass]);
        $record->string_backed_enum = $value;
        $record->save();

        AssociateAction::configureUsing(
            static fn (AssociateAction $action) => $action->recordSelectOptionsQuery(
                static fn (Builder $query): Builder => $query->withCasts(['string_backed_enum' => $enumClass]),
            ),
            during: function () use ($ownerRecord, $record, $value, $title): void {
                livewire(EnumTitlePostsRelationManager::class, ['ownerRecord' => $ownerRecord, 'pageClass' => EditUser::class])
                    ->mountAction(TestAction::make(AssociateAction::class)->table())
                    ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($record, $value, $title): bool {
                        expect($select->getOptions())->toBe([$record->getKey() => $title])
                            ->and($select->getSearchResults($value))->toBe([$record->getKey() => $title]);

                        $select->state($record->getKey());

                        expect($select->getOptionLabel())->toBe($title);

                        return true;
                    });
            },
        );
    })->with([
        'backing value' => [StringBackedEnum::class, 'two', 'two'],
        'label' => [NavigationGroupEnum::class, 'settings', 'System Settings'],
    ]);

    it('preserves the model label fallback for null and blank inherited record select labels', function (?string $title): void {
        $ownerRecord = User::factory()->create();
        $record = Post::factory()->create(['author_id' => null, 'content' => $title]);

        $component = livewire(NullableTitlePostsRelationManager::class, ['ownerRecord' => $ownerRecord, 'pageClass' => EditUser::class]);

        expect($component->instance()->getTable()->getRecordTitle($record))->toBe($title ?? 'post');

        $component
            ->mountAction(TestAction::make(AssociateAction::class)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($record): bool {
                expect($select->getOptions())->toBe([$record->getKey() => 'post']);

                $select->state($record->getKey());

                expect($select->getOptionLabel())->toBe('post');

                return true;
            });
    })->with([null, '', ' ']);

    it('inherits a resource table `recordTitle()` until the relation manager explicitly configures its own title', function (): void {
        $ownerRecord = User::factory()->create();
        $record = Post::factory()->create(['author_id' => null, 'title' => 'Article', 'content' => 'Contextual title']);
        $inheritedTitle = 'Table: Article';

        $component = livewire(TableTitlePostsRelationManager::class, ['ownerRecord' => $ownerRecord, 'pageClass' => EditUser::class]);
        $table = $component->instance()->getTable();

        expect($table->getRecordTitle($record))->toBe($inheritedTitle);

        $component
            ->mountAction(TestAction::make(AssociateAction::class)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($record, $inheritedTitle): bool {
                expect($select->getOptions())->toBe([$record->getKey() => $inheritedTitle]);

                return true;
            });

        expect($table->recordTitleAttribute('title')->getRecordTitle($record))->toBe('Article')
            ->and($table->recordTitleAttribute(static fn (): string => 'content')->getRecordTitle($record))->toBe('Contextual title')
            ->and($table->recordTitleAttribute(null)->getRecordTitle($record))->toBe('post');
    });

    it('keeps explicit `recordTitle()` precedence over `recordTitleAttribute()` in either order', function (bool $isAttributeConfiguredFirst): void {
        $ownerRecord = User::factory()->create();
        $record = Post::factory()->create(['author_id' => null, 'title' => 'Article', 'content' => 'Contextual title']);
        $table = livewire(TableTitlePostsRelationManager::class, ['ownerRecord' => $ownerRecord, 'pageClass' => EditUser::class])->instance()->getTable();

        if ($isAttributeConfiguredFirst) {
            $table->recordTitleAttribute('title');
        }

        $table->recordTitle(static fn (Post $record): string => "Explicit: {$record->content}");

        if (! $isAttributeConfiguredFirst) {
            $table->recordTitleAttribute('title');
        }

        expect($table->getRecordTitle($record))->toBe('Explicit: Contextual title');
    })->with([true, false]);
});

describe('scoped default action URLs', function (): void {
    it('only mounts the explicitly targeted embedded instance', function (string $parameter): void {
        $ticket = Ticket::factory()->hasAttached(Department::factory())->create();

        Livewire::withQueryParams([
            $parameter => 'view',
            'actionComponent' => 'departments',
            'tableActionRecord' => (string) $ticket->departments->first()->getKey(),
        ]);

        livewire(EditTicket::class, ['record' => $ticket->getKey()])
            ->call('mountDefaultActions')
            ->assertActionNotMounted();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->call('mountDefaultActions')
            ->assertActionNotMounted();

        livewire(ScopedDepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'actionUrlIdentifier' => 'other-departments',
        ])
            ->call('mountDefaultActions')
            ->assertActionNotMounted();

        livewire(ScopedDepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->call('mountDefaultActions')
            ->assertSet('mountedActions.0.name', 'view')
            ->call('unmountAction')
            ->call('$refresh')
            ->call('mountDefaultActions')
            ->assertActionNotMounted();
    })->with(['action', 'tableAction']);

    it('leaves untargeted embedded actions closed', function (string $identifier): void {
        $ticket = Ticket::factory()->create();
        Livewire::withQueryParams(['action' => 'view']);

        livewire(ScopedDepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'actionUrlIdentifier' => $identifier,
        ])
            ->call('mountDefaultActions')
            ->assertActionNotMounted();
    })->with(['departments', 'page']);

    it('waits for a deferred table before consuming its URL action', function (): void {
        $ticket = Ticket::factory()->hasAttached(Department::factory())->create();
        Livewire::withQueryParams([
            'tableAction' => 'view',
            'actionComponent' => 'departments',
            'tableActionRecord' => (string) $ticket->departments->first()->getKey(),
            'tableActionArguments' => ['source' => 'email'],
        ]);

        livewire(ScopedDepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
            'deferTableLoading' => true,
        ])
            ->call('mountDefaultActions')
            ->assertActionNotMounted()
            ->call('loadTable')
            ->call('mountDefaultActions')
            ->assertSet('mountedActions.0.name', 'view')
            ->assertSet('mountedActions.0.arguments.source', 'email')
            ->assertSet('mountedActions.0.context.mountedFromUrl', true)
            ->call('unmountAction')
            ->call('loadTable')
            ->call('mountDefaultActions')
            ->assertActionNotMounted();
    });

    it('preserves record action authorization for URL mounts', function (): void {
        app()->bind(DepartmentPolicy::class . '::view', fn (): bool => false);
        $ticket = Ticket::factory()->hasAttached(Department::factory())->create();
        Livewire::withQueryParams([
            'tableAction' => 'view',
            'actionComponent' => 'departments',
            'tableActionRecord' => (string) $ticket->departments->first()->getKey(),
        ]);

        livewire(ScopedDepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->call('mountDefaultActions')
            ->assertActionNotMounted();
    });

    it('rejects foreign, query-excluded, deleted, unknown and malformed URL records', function (string $recordState): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();

        if ($recordState === 'excluded') {
            $ticket->departments()->attach($department);
            $department->update(['name' => 'Excluded department']);
        }

        if ($recordState === 'deleted') {
            $ticket->departments()->attach($department);
            $department->forceDelete();
        }

        Livewire::withQueryParams([
            'tableAction' => 'view',
            'actionComponent' => 'departments',
            'tableActionRecord' => match ($recordState) {
                'malformed' => [$department->getKey()],
                'unknown' => (string) (Department::query()->max('id') + 1),
                default => (string) $department->getKey(),
            },
        ]);

        livewire(ScopedDepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->call('mountDefaultActions')
            ->assertActionNotMounted();
    })->with(['foreign', 'excluded', 'deleted', 'unknown', 'malformed']);
});

describe('attaching records', function (): void {
    it('can attach a single record with table select', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();

        livewire(DepartmentsWithAttachTableSelectRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $department->getKey(),
            ])
            ->assertHasNoFormErrors();

        assertDatabaseHas('department_ticket', [
            'department_id' => $department->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    });

    it('can attach multiple records with table select', function (): void {
        $ticket = Ticket::factory()->create();
        $departments = Department::factory(3)->create();

        livewire(DepartmentsWithAttachTableSelectRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $departments->pluck('id')->toArray(),
            ])
            ->assertHasNoFormErrors();

        foreach ($departments as $department) {
            assertDatabaseHas('department_ticket', [
                'department_id' => $department->getKey(),
                'ticket_id' => $ticket->getKey(),
            ]);
        }
    });

    it('rejects out-of-scope `recordId` when `tableSelect()` is paired with `recordSelectOptionsQuery()`', function (): void {
        $ticket = Ticket::factory()->create();
        Department::factory()->create(['name' => 'Active Engineering']);
        $outOfScopeDepartment = Department::factory()->create(['name' => 'Inactive Department']);

        livewire(DepartmentsWithAttachTableSelectAndModifiedQueryRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $outOfScopeDepartment->getKey(),
            ]);

        assertDatabaseMissing('department_ticket', [
            'department_id' => $outOfScopeDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    });

    it('can attach records when some are already related', function (): void {
        $ticket = Ticket::factory()->create();
        $alreadyAttachedDepartment = Department::factory()->create();
        $newDepartment = Department::factory()->create();

        // First, attach a department to the ticket
        $ticket->departments()->attach($alreadyAttachedDepartment);

        // Verify initial state
        expect($ticket->departments()->count())->toBe(1);

        // Now attach only the new department (the UI would filter out already-attached ones)
        livewire(DepartmentsWithAttachTableSelectRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $newDepartment->getKey(),
            ])
            ->assertHasNoFormErrors();

        // Verify the new department was attached
        assertDatabaseHas('department_ticket', [
            'department_id' => $newDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);

        // Verify total count is now 2
        expect($ticket->departments()->count())->toBe(2);
    });
});

describe('record access edge cases', function (): void {
    it('can access record for action after record no longer matches `TrashedFilter` in `BelongsToMany` relation manager', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();
        $ticket->departments()->attach($department);

        $department->delete();

        livewire(DepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->filterTable('trashed', false)
            ->assertCanSeeTableRecords([$department])
            ->tap(fn () => $department->restore())
            ->callAction(TestAction::make(DeleteAction::class)->table($department));

        expect($department->fresh()->trashed())->toBeTrue();
    });

    it('can access record for action after record no longer matches tab query in `BelongsToMany` relation manager', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create(['name' => 'Accounting']);
        $ticket->departments()->attach($department);

        livewire(DepartmentsRelationManagerWithTabs::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->set('activeTab', 'a_names')
            ->assertCanSeeTableRecords([$department])
            ->tap(fn () => $department->update(['name' => 'Billing']))
            ->callAction(TestAction::make(DeleteAction::class)->table($department));

        expect($department->fresh()->trashed())->toBeTrue();
    });

    it('cannot access record for action after record no longer matches tab without `excludeQueryWhenResolvingRecord()` in `BelongsToMany` relation manager', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create(['name' => 'Accounting']);
        $ticket->departments()->attach($department);

        livewire(DepartmentsRelationManagerWithTabs::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->set('shouldExcludeTabQueryWhenResolvingRecord', false)
            ->set('activeTab', 'a_names')
            ->assertCanSeeTableRecords([$department])
            ->tap(fn () => $department->update(['name' => 'Billing']));

        livewire(DepartmentsRelationManagerWithTabs::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->set('shouldExcludeTabQueryWhenResolvingRecord', false)
            ->set('activeTab', 'a_names')
            ->mountTableAction(DeleteAction::class, $department)
            ->assertTableActionNotMounted(DeleteAction::class);
    });
});

// https://github.com/filamentphp/filament/issues/19594
it('can summarize pivot columns in a `BelongsToMany` `RelationManager`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->create();

    // Attach departments with pivot data
    $ticket->departments()->attach($departments[0], ['quantity' => 10, 'price' => 1000]);
    $ticket->departments()->attach($departments[1], ['quantity' => 20, 'price' => 2000]);
    $ticket->departments()->attach($departments[2], ['quantity' => 30, 'price' => 3000]);

    // Test both implicit (`quantity`) and explicit (`pivot.price`) pivot column summarizers
    livewire(DepartmentsWithPivotSummaryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertSuccessful()
        ->assertTableColumnSummarySet('quantity', 'quantity_sum', 60)    // 10 + 20 + 30
        ->assertTableColumnSummarySet('pivot.price', 'price_sum', 6000); // 1000 + 2000 + 3000
});

it('preserves `modifyQueryUsing()` `addSelect()` subqueries when resolving a `BelongsToMany` record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department);

    livewire(DepartmentsWithModifiedQueryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertTableColumnStateSet('virtual_label', 'preserved', $department->getKey());
});

it('still resolves pivot columns on a single `BelongsToMany` record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department, ['quantity' => 42, 'price' => 1234]);

    livewire(DepartmentsWithPivotSummaryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertTableColumnStateSet('quantity', 42, $department->getKey())
        ->assertTableColumnStateSet('pivot.price', 1234, $department->getKey());
});

it('resolves pivot columns alongside `modifyQueryUsing()` virtual columns on a single `BelongsToMany` record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create(['name' => 'Engineering']);
    $ticket->departments()->attach($department, ['quantity' => 7, 'price' => 99]);

    livewire(DepartmentsWithPivotAndModifiedQueryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertTableColumnStateSet('name', 'Engineering', $department->getKey())
        ->assertTableColumnStateSet('quantity', 7, $department->getKey())
        ->assertTableColumnStateSet('pivot.price', 99, $department->getKey())
        ->assertTableColumnStateSet('virtual_label', 'preserved', $department->getKey());
});

it('resolves pivot columns and `modifyQueryUsing()` virtual columns across multiple `BelongsToMany` records', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(2)->create();
    $ticket->departments()->attach($departments[0], ['quantity' => 10, 'price' => 100]);
    $ticket->departments()->attach($departments[1], ['quantity' => 20, 'price' => 200]);

    livewire(DepartmentsWithPivotAndModifiedQueryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($departments)
        ->assertTableColumnStateSet('quantity', 10, $departments[0]->getKey())
        ->assertTableColumnStateSet('quantity', 20, $departments[1]->getKey())
        ->assertTableColumnStateSet('pivot.price', 100, $departments[0]->getKey())
        ->assertTableColumnStateSet('pivot.price', 200, $departments[1]->getKey())
        ->assertTableColumnStateSet('virtual_label', 'preserved', $departments[0]->getKey())
        ->assertTableColumnStateSet('virtual_label', 'preserved', $departments[1]->getKey());
});

it('resolves a `BelongsToMany` record with the related model\'s primary key when `modifyQueryUsing()` adds a subquery via `addSelect()`', function (): void {
    Department::factory()->count(2)->create();
    $department = Department::factory()->create();
    $ticket = Ticket::factory()->create();
    $ticket->departments()->attach($department);

    $resolved = livewire(DepartmentsWithSubquerySelectAndDetachRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->instance()
        ->getTableRecord((string) $department->getKey());

    expect($resolved)->not->toBeNull();
    expect($resolved->getKey())->toBe($department->getKey());
});

it('passes the related model with the correct primary key into a `BelongsToMany` `DetachAction` when `modifyQueryUsing()` adds a subquery via `addSelect()`', function (): void {
    Department::factory()->count(2)->create();
    $department = Department::factory()->create();
    $ticket = Ticket::factory()->create();
    $ticket->departments()->attach($department);

    livewire(DepartmentsWithSubquerySelectAndDetachRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->callAction(TestAction::make(DetachAction::class)->table($department));

    expect($ticket->refresh()->departments)->toHaveCount(0);
});

it('can summarize both pivot and non-pivot columns in a `BelongsToMany` `RelationManager`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->create();

    $ticket->departments()->attach($departments[0], ['quantity' => 10, 'price' => 1000]);
    $ticket->departments()->attach($departments[1], ['quantity' => 20, 'price' => 2000]);
    $ticket->departments()->attach($departments[2], ['quantity' => 30, 'price' => 3000]);

    livewire(DepartmentsWithMixedSummaryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertSuccessful()
        ->assertTableColumnSummarySet('name', 'name_count', 3)
        ->assertTableColumnSummarySet('quantity', 'quantity_sum', 60)
        ->assertTableColumnSummarySet('pivot.price', 'price_sum', 6000);
});

it('resolves nullable relation manager lazy loading configuration', function (?bool $configuration, bool $legacyConfiguration, bool $expected): void {
    $relationManager = new class extends DepartmentsRelationManager
    {
        public static ?bool $isLazyLoaded = null;

        public static bool $isLazy = true;
    };

    $relationManager::$isLazyLoaded = $configuration;
    $relationManager::$isLazy = $legacyConfiguration;

    expect($relationManager::getDefaultProperties())->toBe($expected ? ['lazy' => true] : [])
        ->and($relationManager::isLazyLoaded())->toBe($expected)
        ->and($relationManager::$isLazyLoaded)->toBe($configuration);
})->with([
    'unset with lazy legacy default' => [null, true, true],
    'unset with eager legacy default' => [null, false, false],
    'explicit true overrides eager legacy default' => [true, false, true],
    'explicit false overrides lazy legacy default' => [false, true, false],
]);

it('preserves legacy relation manager loading overrides while prioritizing explicit new configuration', function (): void {
    $legacyRelationManager = new class extends DepartmentsRelationManager
    {
        public static function isLazy(): bool
        {
            return false;
        }
    };

    $configuredRelationManager = new class extends DepartmentsRelationManager
    {
        protected static ?bool $isLazyLoaded = false;

        public static function isLazy(): bool
        {
            return true;
        }
    };

    $overriddenRelationManager = new class extends DepartmentsRelationManager
    {
        public static ?bool $isLazyLoaded = null;

        public static function isLazyLoaded(): bool
        {
            return false;
        }
    };

    expect($legacyRelationManager::getDefaultProperties())->toBe([])
        ->and($configuredRelationManager::getDefaultProperties())->toBe([])
        ->and($overriddenRelationManager::getDefaultProperties())->toBe([]);

    $overriddenRelationManager::$isLazyLoaded = true;

    expect($overriddenRelationManager::getDefaultProperties())->toBe([]);
});

it('defers the tab badge loading when `$isBadgeDeferred` is `true`', function (): void {
    $ticket = Ticket::factory()->create();

    $tab = DepartmentsWithDeferredBadgeRelationManager::getTabComponent($ticket, EditTicket::class);

    expect($tab->isBadgeDeferred())->toBeTrue()
        ->and($tab->isBadgeLazyLoaded())->toBeTrue();
});

it('uses `$isBadgeLazyLoaded` in preference to the legacy badge property', function (): void {
    $ticket = Ticket::factory()->create();

    $lazyRelationManager = new class extends DepartmentsRelationManager
    {
        protected static ?bool $isBadgeLazyLoaded = true;
    };

    $eagerRelationManager = new class extends DepartmentsWithDeferredBadgeRelationManager
    {
        protected static ?bool $isBadgeLazyLoaded = false;
    };

    $eagerRelationManagerWithLegacyGetter = new class extends DepartmentsRelationManager
    {
        protected static ?bool $isBadgeLazyLoaded = false;

        public static function isBadgeDeferred(Model $ownerRecord, string $pageClass): bool
        {
            return true;
        }
    };

    expect($lazyRelationManager::getTabComponent($ticket, EditTicket::class)->isBadgeLazyLoaded())->toBeTrue()
        ->and($lazyRelationManager::isBadgeDeferred($ticket, EditTicket::class))->toBeTrue()
        ->and($eagerRelationManager::getTabComponent($ticket, EditTicket::class)->isBadgeLazyLoaded())->toBeFalse()
        ->and($eagerRelationManager::isBadgeDeferred($ticket, EditTicket::class))->toBeFalse()
        ->and($eagerRelationManagerWithLegacyGetter::getTabComponent($ticket, EditTicket::class)->isBadgeLazyLoaded())->toBeFalse();
});

it('respects both legacy and new relation manager badge getter overrides', function (): void {
    $ticket = Ticket::factory()->create();

    $relationManagers = [
        new class extends DepartmentsRelationManager
        {
            public static function isBadgeDeferred(Model $ownerRecord, string $pageClass): bool
            {
                return true;
            }
        },
        new class extends DepartmentsRelationManager
        {
            public static function isBadgeLazyLoaded(Model $ownerRecord, string $pageClass): bool
            {
                return true;
            }
        },
        new class extends DepartmentsRelationManager
        {
            protected static ?bool $isBadgeLazyLoaded = false;

            public static function isBadgeLazyLoaded(Model $ownerRecord, string $pageClass): bool
            {
                return true;
            }
        },
    ];

    foreach ($relationManagers as $relationManager) {
        expect($relationManager::getTabComponent($ticket, EditTicket::class)->isBadgeLazyLoaded())->toBeTrue();
    }
});

it('shares relation group configuration between `lazyLoadedBadge()` and `deferBadge()`', function (string $method): void {
    $group = RelationGroup::make('Departments', [DepartmentsRelationManager::class])
        ->badge(static fn (): string => '3')
        ->{$method}(static fn (): bool => true);

    expect($group->isBadgeLazyLoaded())->toBeTrue()
        ->and($group->isBadgeDeferred())->toBeTrue()
        ->and($group->getTabComponent()->isBadgeLazyLoaded())->toBeTrue()
        ->and($group->getTabComponent()->getBadge())->toBe('3')
        ->and($group->lazyLoadedBadge(false)->getTabComponent()->isBadgeLazyLoaded())->toBeFalse()
        ->and($group->deferBadge()->lazyLoadedBadge(false)->isBadgeDeferred())->toBeFalse();
})->with(['lazyLoadedBadge', 'deferBadge']);

it('respects an existing relation group `isBadgeDeferred()` override', function (): void {
    $group = new class('Departments', []) extends RelationGroup
    {
        public function isBadgeDeferred(): bool
        {
            return true;
        }
    };

    expect($group->getTabComponent()->isBadgeLazyLoaded())->toBeTrue();
});

it('resolves the deferred tab badge value from `getBadge()`', function (): void {
    $ticket = Ticket::factory()
        ->hasAttached(Department::factory(3))
        ->create();

    $tab = DepartmentsWithDeferredBadgeRelationManager::getTabComponent($ticket, EditTicket::class);

    expect($tab->isBadgeDeferred())->toBeTrue()
        ->and($tab->getBadge())->toBe('3');
});

it('does not defer the tab badge loading by default', function (): void {
    $ticket = Ticket::factory()->create();

    $tab = DepartmentsRelationManager::getTabComponent($ticket, EditTicket::class);

    expect($tab->isBadgeDeferred())->toBeFalse();
});

it('can customize relation manager tabs without combining them with content', function (): void {
    $component = livewire(NonCombinedContentPage::class, ['record' => Ticket::factory()->create()->getRouteKey()])
        ->assertSuccessful();

    $tabs = $component->instance()->getSchema('content')->getComponents()[1]->getChildSchema()->getComponents(withOriginalKeys: true);

    expect(array_keys($tabs))->toBe([3, 'departments'])
        ->and($tabs[3]->getLabel())->toBe('Factory departments')
        ->and($tabs['departments']->getLabel())->toBe('Factory departments');
});

describe('combined tabs', function (): void {
    it('preserves the default content key and singular customization hooks', function (): void {
        $component = livewire(DefaultCombinedContentPage::class, ['record' => Ticket::factory()->create()->getRouteKey()])
            ->assertSuccessful();

        $tabs = $component->instance()->getSchema('content')->getComponents()[0]->getChildSchema()->getComponents(withOriginalKeys: true);

        expect(array_keys($tabs))->toBe(['', 0])
            ->and($tabs['']->getLabel())->toBe('Legacy content')
            ->and($tabs['']->getIcon())->toBe('heroicon-m-pencil')
            ->and($tabs['']->getBadge())->toBe('Legacy badge');
    });

    it('preserves sparse numeric and named manager keys in the default combined order', function (bool $isContentAfter): void {
        $component = livewire(EditTicketWithContentTabs::class, ['record' => Ticket::factory()->create()->getRouteKey()])
            ->set('hasCustomTabs', false)
            ->set('isContentAfter', $isContentAfter)
            ->assertSuccessful();

        $tabs = $component->instance()->getSchema('content')->getComponents()[0]->getChildSchema()->getComponents(withOriginalKeys: true);

        expect(array_keys($tabs))->toBe($isContentAfter
            ? [3, 'departments', 9, '']
            : ['', 3, 'departments', 9])
            ->and($tabs['']->getBadge())->toBe('Primary')
            ->and($tabs[3]->getBadge())->toBe('Managers')
            ->and(array_keys($component->instance()->getRelationManagerTabComponents(except: ['3', 'departments'])))->toBe([9]);
    })->with([false, true]);

    it('preserves named manager keys and the legacy empty content slot', function (string $managerKey, bool $isContentAfter): void {
        KeyedCombinedContentPage::$managerKey = $managerKey;

        $component = livewire(KeyedCombinedContentPage::class, ['record' => Ticket::factory()->create()->getRouteKey()])
            ->set('isContentAfter', $isContentAfter)
            ->assertSuccessful();

        $tabs = $component->instance()->getSchema('content')->getComponents()[0]->getChildSchema()->getComponents(withOriginalKeys: true);

        expect(array_keys($tabs))->toBe($managerKey === '' ? [''] : ($isContentAfter ? [$managerKey, ''] : ['', $managerKey]))
            ->and($tabs['']->getLabel())->toBe('Legacy content');

        if ($managerKey !== '') {
            $component
                ->set('activeRelationManager', $managerKey)
                ->assertSuccessful();
        }
    })->with(['', 'content:view'])->with([false, true]);

    it('interleaves content and generated manager or group tabs while rendering only the active content', function (bool $hasGroupedManagers): void {
        $component = livewire(EditTicketWithContentTabs::class, ['record' => Ticket::factory()->create()->getRouteKey()])
            ->set('hasGroupedManagers', $hasGroupedManagers)
            ->assertSuccessful();

        $tabs = $component->instance()->getSchema('content')->getComponents()[0]->getChildSchema()->getComponents(withOriginalKeys: true);

        expect(array_keys($tabs))->toBe(['view', 3, '', 'departments', 'preferences', 9]);

        $component
            ->set('activeRelationManager', 'view')
            ->assertSee('Ticket overview')
            ->assertDontSee('Preferences not saved')
            ->assertDontSee('Configured departments (fr)')
            ->set('activeRelationManager', 'preferences')
            ->assertSee('Preferences not saved')
            ->assertDontSee('Ticket overview')
            ->set('activeRelationManager', '3')
            ->assertSee('Configured departments (fr)')
            ->assertDontSee('Preferences not saved')
            ->assertDontSee('Ticket overview');
    })->with([false, true]);

    it('retains configured grouped managers, badges, filtering and lazy defaults', function (): void {
        $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Visible department']))->create();

        $component = livewire(MixedAuthorizationContentPage::class, ['record' => $ticket->getRouteKey()])
            ->set('hasGroupedManagers', true)
            ->set('activeRelationManager', '3')
            ->assertSee('Configured departments (fr)')
            ->assertSee('Secondary departments (fr)')
            ->assertSee('Visible department')
            ->assertSee('Grouped')
            ->assertDontSee('Denied departments');

        $tabs = $component->instance()->getSchema('content')->getComponents()[0]->getChildSchema()->getComponents(withOriginalKeys: true);
        $groupedManagers = $tabs[3]->getChildSchema()->getComponents();

        expect(array_map(static fn ($manager): ?string => $manager->getKey(isAbsolute: false), $groupedManagers))->toBe([
            ContentTabsDepartmentsRelationManager::class . '-2',
            ContentTabsDepartmentsRelationManager::class . '-secondary',
        ])
            ->and($groupedManagers[0]->getComponentProperties()['lazy'])->toBeFalse()
            ->and($tabs['departments']->getChildSchema()->getComponents()[0]->getComponentProperties()['lazy'])->toBeTrue();

        app()->bind(DepartmentPolicy::class . '::viewAny', static fn (): bool => false);

        $deniedComponent = livewire(MixedAuthorizationContentPage::class, ['record' => $ticket->getRouteKey()])
            ->set('hasGroupedManagers', true)
            ->assertDontSee('Managers')
            ->assertDontSee('Department group')
            ->assertDontSee('Visible department')
            ->set('activeRelationManager', 'view')
            ->assertSee('Ticket overview');

        expect($deniedComponent->instance()->getRelationManagerTabComponent(3))->toBeNull()
            ->and($deniedComponent->instance()->getRelationManagerTabComponent('departments'))->toBeNull()
            ->and($deniedComponent->instance()->getRelationManagerTabComponent('missing'))->toBeNull();
    });

    it('renders content tabs without managers and excludes hidden or removed tabs', function (): void {
        livewire(EditTicketWithContentTabs::class, ['record' => Ticket::factory()->create()->getRouteKey()])
            ->set('hasManagers', false)
            ->set('activeRelationManager', 'preferences')
            ->assertSee('Preferences not saved')
            ->set('isPreferencesHidden', true)
            ->assertDontSee('Preferences not saved')
            ->set('isPreferencesHidden', false)
            ->assertSee('Preferences not saved')
            ->set('isPreferencesRemoved', true)
            ->assertDontSee('Preferences not saved')
            ->set('activeRelationManager', 'view')
            ->assertSee('Ticket overview');
    });

    it('switches resource content with URL reload and history and submits named forms independently', function (): void {
        Artisan::call('filament:assets');
        $ticket = Ticket::factory()->hasAttached(Department::factory()->state(['name' => 'Browser department']))->create();
        $url = "/tickets/{$ticket->getRouteKey()}/content-tabs";

        $page = visit($url);
        $page->assertVisible('#edit-ticket-tab')
            ->fill('#edit-note', 'Updated ticket')
            ->click('[data-testid="save-ticket"]')
            ->assertSee('Edit saved')
            ->click('[role="tab"][aria-controls="overview-tab"]')
            ->assertVisible('#overview-tab')
            ->assertMissing('#edit-ticket-tab input')
            ->assertQueryStringHas('relation', 'view')
            ->refresh()
            ->assertVisible('#overview-tab')
            ->assertNoAccessibilityIssues();

        visit($url . '?relation=view')->inDarkMode()
            ->assertVisible('#overview-tab')
            ->assertNoAccessibilityIssues();

        $page->click('[role="tab"][aria-controls="preferences-tab"]')
            ->assertVisible('#preferences-tab')
            ->fill('#preference-nickname', 'Independent preference')
            ->click('Save preferences')
            ->assertSee('Independent preference')
            ->click('[role="tab"][aria-controls="edit-ticket-tab"]')
            ->assertVisible('#edit-ticket-tab')
            ->assertSee('Edit not saved')
            ->assertQueryStringMissing('relation')
            ->fill('#edit-note', 'Updated ticket')
            ->click('[data-testid="save-ticket"]')
            ->assertSee('Edit saved')
            ->click('[role="tab"][aria-controls="preferences-tab"]')
            ->assertVisible('#preferences-tab')
            ->assertSee('Independent preference')
            ->assertNoAccessibilityIssues();

        visit($url . '?relation=preferences')->inDarkMode()
            ->assertVisible('#preferences-tab')
            ->assertNoAccessibilityIssues();

        $page->click('[role="tab"][aria-controls="edit-ticket-tab"]')
            ->assertVisible('#edit-ticket-tab')
            ->refresh()
            ->assertVisible('#edit-ticket-tab')
            ->assertQueryStringMissing('relation')
            ->click('[role="tab"][aria-controls="configured-manager-tab"]')
            ->assertSee('Browser department')
            ->assertQueryStringHas('relation', '3')
            ->refresh()
            ->assertSee('Browser department')
            ->navigate($url . '?relation=view')
            ->assertVisible('#overview-tab')
            ->back()
            ->assertSee('Browser department')
            ->forward()
            ->assertVisible('#overview-tab')
            ->click('[role="tab"][aria-controls="preferences-tab"]')
            ->assertVisible('#preferences-tab')
            ->click('Hide preferences')
            ->assertVisible('#overview-tab')
            ->assertQueryStringHas('relation', 'view')
            ->navigate($url . '?relation=view')
            ->assertVisible('#overview-tab')
            ->click('[role="tab"][aria-controls="preferences-tab"]')
            ->assertVisible('#preferences-tab')
            ->click('Remove preferences')
            ->assertVisible('#overview-tab')
            ->navigate($url . '?no-managers=1&hidden=1&relation=preferences')
            ->assertVisible('#overview-tab')
            ->navigate($url . '?no-managers=1&removed=1&relation=preferences')
            ->assertVisible('#overview-tab')
            ->assertNoSmoke();
    });
});

class RecordUrlResolutionRelationManager extends RelationManager
{
    /** @var array<int | string | null> */
    public static array $initialActionRecordKeys = [];

    public bool $areActionsGrouped = false;

    public bool $areActionsNested = false;

    public bool $hasCustomRecordAction = false;

    public bool $hasRecordDependentNestedGroupVisibility = false;

    public bool $hasRecordDependentRootGroupTriggerView = false;

    public bool $isTableLoadingDeferred = false;

    public bool $isNestedGroupHidden = false;

    protected static string $relationship = 'departments';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        $viewAction = Action::make('view')
            ->authorize(static fn (Department $record): bool => in_array($record->name, ['View URL', 'View action']))
            ->url(static fn (Department $record): ?string => $record->name === 'View URL' ? "/departments/{$record->getKey()}/view" : null)
            ->openUrlInNewTab(static fn (Department $record): bool => $record->name === 'View URL');
        $editAction = Action::make('edit')
            ->authorize(static fn (Department $record): bool => in_array($record->name, ['Edit URL', 'Edit action']))
            ->url(static fn (Department $record): ?string => $record->name === 'Edit URL' ? "/departments/{$record->getKey()}/edit" : null);

        static::$initialActionRecordKeys[] = $viewAction->getRecord(withDefault: false)?->getKey();
        static::$initialActionRecordKeys[] = $editAction->getRecord(withDefault: false)?->getKey();

        $recordActions = match (true) {
            $this->areActionsNested => ActionGroup::make([
                Action::make('other'),
                $viewAction,
                ActionGroup::make([$editAction])
                    ->hidden(fn (Department $record): bool => $this->isNestedGroupHidden || ($this->hasRecordDependentNestedGroupVisibility && ($record->name !== 'Edit URL'))),
            ]),
            $this->areActionsGrouped => ActionGroup::make([$viewAction, $editAction]),
            default => [$viewAction, $editAction],
        };

        if (($recordActions instanceof ActionGroup) && $this->hasRecordDependentRootGroupTriggerView) {
            $recordActions->defaultTriggerView(static fn (Department $record): string => $record->name === 'View URL'
                ? ActionGroup::BUTTON_VIEW
                : ActionGroup::ICON_BUTTON_VIEW);
        }

        return $table
            ->columns([
                TextColumn::make('name'),
            ])
            ->recordActions($recordActions)
            ->when($this->hasCustomRecordAction, fn (Table $table): Table => $table->recordAction(null))
            ->defaultSort('departments.id')
            ->openRecordUrlInNewTab(static fn (Department $record): bool => $record->name === 'View URL')
            ->deferLoading($this->isTableLoadingDeferred);
    }
}

class AuthorizationRelationManager extends DepartmentsRelationManager
{
    /** @var array<string> */
    public static array $calls = [];

    /** @var array<array{int|string, string}> */
    public static array $authorizationArguments = [];

    public ?string $name = null;

    protected static bool $isLazy = false;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        static::$calls[] = 'authorize';
        static::$authorizationArguments[] = [$ownerRecord->getKey(), $pageClass];

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public function boot(): void
    {
        static::$calls[] = 'boot';
    }

    public function mount(): void
    {
        static::$calls[] = 'mount';

        parent::mount();
    }

    public function hydrate(): void
    {
        static::$calls[] = 'hydrate';
    }

    public function updatingName(): void
    {
        static::$calls[] = 'updating';
    }

    public function updatedName(): void
    {
        static::$calls[] = 'updated';
    }

    public function table(Table $table): Table
    {
        static::$calls[] = 'table';

        return parent::table($table);
    }

    public function render(): View
    {
        static::$calls[] = 'render';

        return parent::render();
    }

    public function sensitiveAction(): void
    {
        static::$calls[] = 'action';
    }
}

class RelationManagerAuthorizationPage extends EditTicket
{
    public static bool $grouped = false;

    protected function getAllRelationManagers(): array
    {
        return static::$grouped
            ? [RelationGroup::make('Departments', [AuthorizationRelationManager::make()])]
            : [AuthorizationRelationManager::class];
    }
}

class DefaultCombinedContentPage extends EditTicket
{
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Legacy content';
    }

    public function getContentTabIcon(): ?string
    {
        return 'heroicon-m-pencil';
    }

    public function getContentTabComponent(): Tab
    {
        return parent::getContentTabComponent()->badge('Legacy badge');
    }

    protected function getAllRelationManagers(): array
    {
        return [ContentTabsDepartmentsRelationManager::class];
    }
}

class KeyedCombinedContentPage extends DefaultCombinedContentPage
{
    public static string $managerKey = '';

    public bool $isContentAfter = false;

    public function getContentTabPosition(): ?ContentTabPosition
    {
        return $this->isContentAfter ? ContentTabPosition::After : null;
    }

    protected function getAllRelationManagers(): array
    {
        return [static::$managerKey => (static::$managerKey === '')
            ? RelationGroup::make('Unused manager', [ContentTabsDepartmentsRelationManager::class])
                ->tab(static fn (): Tab => throw new LogicException('The legacy empty-key manager tab must not be constructed.'))
            : ContentTabsDepartmentsRelationManager::class];
    }
}

class MixedAuthorizationContentPage extends EditTicketWithContentTabs
{
    protected function getAllRelationManagers(): array
    {
        if (! $this->hasGroupedManagers) {
            return parent::getAllRelationManagers();
        }

        return array_replace(parent::getAllRelationManagers(), [
            3 => RelationGroup::make('Department group', [
                2 => ContentTabsDepartmentsRelationManager::make(['heading' => 'Configured departments', 'lazy' => false]),
                'secondary' => ContentTabsDepartmentsRelationManager::make(['heading' => 'Secondary departments', 'lazy' => false]),
                11 => DeniedContentTabsDepartmentsRelationManager::make(['heading' => 'Denied departments', 'lazy' => false]),
            ])->badge('Grouped'),
        ]);
    }
}

class DeniedContentTabsDepartmentsRelationManager extends ContentTabsDepartmentsRelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return false;
    }
}

class NonCombinedContentPage extends EditTicket
{
    public function getTabComponents(): array
    {
        throw new LogicException('The combined tab hook must only be used in combined mode.');
    }

    public function getRelationManagerTabComponent(string | int $key): ?Tab
    {
        return parent::getRelationManagerTabComponent($key)?->label('Factory departments');
    }

    protected function getAllRelationManagers(): array
    {
        return [3 => ContentTabsDepartmentsRelationManager::class, 'departments' => ContentTabsDepartmentsRelationManager::class];
    }
}

class RelatedResourcePostsRelationManager extends PostsWithAssociateActionRelationManager
{
    protected static ?string $relatedResource = PostResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->contentGrid(static fn (RelationManager $livewire): ?array => $livewire->getTable()->hasColumnsLayout() ? ['md' => 2] : null)
            ->recordTitleAttribute('content')
            ->recordActions([])
            ->headerActions([AssociateAction::make()->preloadRecordSelect()]);
    }
}

class RelatedResourceDepartmentsRelationManager extends DepartmentsWithAttachActionRelationManager
{
    protected static ?string $relatedResource = DepartmentWithRecordTitleResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([AttachAction::make()->preloadRecordSelect()]);
    }
}

class DepartmentWithRecordTitleResource extends DepartmentResource
{
    protected static ?string $recordTitleAttribute = 'id';
}

class NullableTitlePostResource extends PostResource
{
    protected static ?string $recordTitleAttribute = 'content';
}

class NullableTitlePostsRelationManager extends RelatedResourcePostsRelationManager
{
    protected static ?string $relatedResource = NullableTitlePostResource::class;
}

class TableTitlePostResource extends PostResource
{
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordTitle(static fn (Post $record): string => "Table: {$record->title}");
    }
}

class TableTitlePostsWithAttributeRelationManager extends RelatedResourcePostsRelationManager
{
    protected static ?string $relatedResource = TableTitlePostResource::class;
}

class AttributeTitlePostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    protected static ?string $relatedResource = PostResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->inverseRelationship('author')
            ->headerActions([AssociateAction::make()->preloadRecordSelect()]);
    }
}

class EnumTitlePostsRelationManager extends AttributeTitlePostsRelationManager
{
    public function table(Table $table): Table
    {
        return parent::table($table)->recordTitleAttribute('string_backed_enum');
    }
}

class TableTitlePostsRelationManager extends AttributeTitlePostsRelationManager
{
    protected static ?string $relatedResource = TableTitlePostResource::class;
}

class AttributeTitleDepartmentResource extends DepartmentResource
{
    protected static ?string $recordTitleAttribute = 'name';
}

class AttributeTitleDepartmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    protected static ?string $relatedResource = AttributeTitleDepartmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->inverseRelationship('tickets')
            ->headerActions([AttachAction::make()->preloadRecordSelect()]);
    }
}

class CustomTitlePostResource extends PostResource
{
    public static function getRecordTitle(?Model $record): ?string
    {
        return $record ? "Resource: {$record->getAttribute('title')}" : null;
    }
}

class CustomTitlePostsRelationManager extends AttributeTitlePostsRelationManager
{
    protected static ?string $relatedResource = CustomTitlePostResource::class;
}

class StaticTitlePostsRelationManager extends CustomTitlePostsRelationManager
{
    protected static ?string $recordTitleAttribute = 'content';
}

class CustomTitlePostsWithAttributeRelationManager extends RelatedResourcePostsRelationManager
{
    protected static ?string $relatedResource = CustomTitlePostResource::class;
}

class CustomTitleDepartmentResource extends AttributeTitleDepartmentResource
{
    public static function getRecordTitle(?Model $record): ?string
    {
        return $record ? "Resource: {$record->getAttribute('name')}" : null;
    }
}

class CustomTitleDepartmentsRelationManager extends AttributeTitleDepartmentsRelationManager
{
    protected static ?string $relatedResource = CustomTitleDepartmentResource::class;
}

class LegacyTitlePostsRelationManager extends PostsWithAssociateActionRelationManager
{
    public function getTableRecordTitle(mixed $post): ?string
    {
        return "Legacy: {$post->getAttribute('title')}";
    }

    public function table(Table $table): Table
    {
        return parent::table($table)->headerActions([AssociateAction::make()->preloadRecordSelect()]);
    }
}

class LegacyTitleDepartmentsRelationManager extends DepartmentsWithAttachActionRelationManager
{
    public function getTableRecordTitle(Model $record): ?string
    {
        return "Legacy: {$record->getAttribute('name')}";
    }

    public function table(Table $table): Table
    {
        return parent::table($table)->headerActions([AttachAction::make()->preloadRecordSelect()]);
    }
}
