<?php

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Events\RecordSaved;
use Filament\Resources\Events\RecordUpdated;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\PostMetadata;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertSchemaStateSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
            'rating' => $post->rating,
        ]);
});

it('can save', function (): void {
    Event::fake([
        RecordUpdated::class,
        RecordSaved::class,
    ]);

    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);

    Event::assertDispatched(RecordUpdated::class);
    Event::assertDispatched(RecordSaved::class);
});

it('can validate input', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'title' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['title' => 'required']);
});

describe('partial saving', function (): void {
    it('does not save a field excluded by an ancestor', function (Closure $layout): void {
        $post = Post::factory()->create(['title' => 'Original title']);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->instance();
        $page->form->components([$layout(TextInput::make('title'))]);
        $field = $page->form->getComponentByStatePath('title', withHidden: true);
        $page->data['title'] = 'Changed title';

        $page->saveFormComponentOnly($field);

        expect($post->refresh()->title)->toBe('Original title');
    })->with([
        'group' => [static fn (Component $component): Group => Group::make([$component])->disabled()],
        'tab' => [static fn (Component $component): Tabs => Tabs::make('Details')->tabs([
            Tab::make('Read only')->disabled()->schema([Section::make()->schema([$component])]),
        ])],
        'unsaved group' => [static fn (Component $component): Group => Group::make([$component])->saved(false)],
        'hidden section' => [static fn (Component $component): Section => Section::make()->hidden()->schema([$component])],
    ]);

    it('can save a field whose disabled ancestor opts into saving', function (): void {
        $post = Post::factory()->create(['title' => 'Original title']);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->instance();
        $page->form->components([
            Group::make([TextInput::make('title')])->disabled()->saved(),
        ]);
        $field = $page->form->getComponentByStatePath('title');
        $page->data['title'] = 'Changed title';

        $page->saveFormComponentOnly($field);

        expect($post->refresh()->title)->toBe('Changed title');
    });

    it('respects ancestor opt-in when saving a relationship component inside a disabled ancestor', function (bool $shouldSaveRelationships): void {
        $post = Post::factory()->create();
        $metadata = PostMetadata::factory()->for($post)->create(['seo_title' => 'Original SEO title']);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->instance();
        $page->form->components([
            Group::make([
                Section::make('Metadata')->key('metadata')->relationship('metadata')->schema([
                    TextInput::make('seo_title'),
                ]),
            ])->disabled()->saveRelationshipsWhenDisabled($shouldSaveRelationships),
        ])->fill($post->attributesToArray());
        $section = $page->form->getComponent('metadata');
        $page->data['metadata']['seo_title'] = 'Changed SEO title';

        $page->saveFormComponentOnly($section);

        expect($metadata->refresh()->seo_title)->toBe($shouldSaveRelationships ? 'Changed SEO title' : 'Original SEO title');
    })->with([false, true]);

    it('validates and saves an eligible section through its footer action', function (string $pageClass): void {
        $post = Post::factory()->create();
        $metadata = PostMetadata::factory()->for($post)->create(['seo_title' => 'Original SEO title']);
        $action = TestAction::make('saveSection')->schemaComponent('details');
        $page = livewire($pageClass, ['record' => $post->getKey()]);

        $page->fillForm(['title' => null])->callAction($action)->assertHasFormErrors(['title' => 'required']);

        expect($post->refresh()->title)->not->toBeNull();

        $page->set('saveEvents', [])
            ->fillForm([
                'title' => 'Changed title',
                'metadata' => ['seo_title' => 'Changed SEO title'],
                'content' => null,
            ])
            ->callAction($action)
            ->assertHasNoFormErrors()
            ->assertNotified('Section saved')
            ->assertSet('saveEvents', ['beforeValidate', 'afterValidate', 'mutate', 'beforeSave', 'afterSave']);

        expect($post->refresh())->title->toBe('Changed title (mutated)')->content->not->toBeNull();
        expect($metadata->refresh()->seo_title)->toBe('Changed SEO title');
    })->with([
        'default schema' => [EditPostWithPartialSaving::class],
        'closure-backed footer' => [EditPostWithPartialSavingInFooter::class],
    ]);
});

it('can delete', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($post);
});

it('can refresh data', function (): void {
    $post = Post::factory()->create();

    $page = livewire(EditPost::class, [
        'record' => $post->getKey(),
    ]);

    $originalPostTitle = $post->title;

    $page->assertSchemaStateSet([
        'title' => $originalPostTitle,
    ]);

    $newPostTitle = Str::random();

    $post->title = $newPostTitle;
    $post->save();

    $page->assertSchemaStateSet([
        'title' => $originalPostTitle,
    ]);

    $page->call('refreshTitle');

    $page->assertSchemaStateSet([
        'title' => $newPostTitle,
    ]);
});

test('actions will not interfere with database transactions on an error', function (): void {
    $post = Post::factory()->create();

    $transactionLevel = DB::transactionLevel();

    try {
        livewire(EditPost::class, [
            'record' => $post->getKey(),
        ])
            ->callAction('randomize_title');
    } catch (Exception $exception) {
        // Inspect the transaction level after the action exception is handled.
    }

    expect(DB::transactionLevel())
        ->toBe($transactionLevel);
});

it('can render ticket messages page without a policy', function (): void {
    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('edit', ['record' => $message]))
        ->assertSuccessful();
});

it('can render page if the policy `viewAny()` returns `true`', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render page if the policy `viewAny()` returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page without a policy if authorization is strict', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('edit', ['record' => $message]))
        ->assertServerError();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);
});

it('does not render page if the policy `viewAny()` returns `false`', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page if the policy `viewAny()` returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('can render page if the policy `update()` returns `true`', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render page if the policy `update()` returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('does not render page if the policy `update()` returns `false`', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('does not render page if the policy `update()` returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('re-authorizes `update()` on Livewire updates after the initial mount', function (): void {
    $ticket = Ticket::factory()
        ->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);

    $component = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $component
        ->set('data.subject', 'foo')
        ->assertStatus(403);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('re-authorizes `viewAny()` on Livewire updates after the initial mount of an edit page', function (): void {
    $ticket = Ticket::factory()
        ->create();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $component = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $component
        ->set('data.subject', 'foo')
        ->assertStatus(403);

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false): void {
    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

    $ticket = Ticket::factory()
        ->create();

    if ($isSoftDeleted) {
        $ticket->delete();
    }

    livewire(EditTicket::class, ['record' => $ticket->getKey()])
        ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}($action);

    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool => true);
})->with([
    'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
    'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
    'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
    'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
    'view action with policy returning true' => fn (): array => [ViewAction::class, 'view', true, true],
    'view action with policy returning allowed response' => fn (): array => [ViewAction::class, 'view', Response::allow(), true],
    'view action with policy returning false' => fn (): array => [ViewAction::class, 'view', false, false],
    'view action with policy returning denied response' => fn (): array => [ViewAction::class, 'view', Response::deny(), false],
    'delete action with policy returning true' => fn (): array => [DeleteAction::class, 'delete', true, true],
    'delete action with policy returning allowed response' => fn (): array => [DeleteAction::class, 'delete', Response::allow(), true],
    'delete action with policy returning false' => fn (): array => [DeleteAction::class, 'delete', false, false],
    'delete action with policy returning denied response' => fn (): array => [DeleteAction::class, 'delete', Response::deny(), false],
    'force delete action with policy returning true' => fn (): array => [ForceDeleteAction::class, 'forceDelete', true, true, true],
    'force delete action with policy returning allowed response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::allow(), true, true],
    'force delete action with policy returning false' => fn (): array => [ForceDeleteAction::class, 'forceDelete', false, false, true],
    'force delete action with policy returning denied response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::deny(), false, true],
    'restore action with policy returning true' => fn (): array => [RestoreAction::class, 'restore', true, true, true],
    'restore action with policy returning allowed response' => fn (): array => [RestoreAction::class, 'restore', Response::allow(), true, true],
    'restore action with policy returning false' => fn (): array => [RestoreAction::class, 'restore', false, false, true],
    'restore action with policy returning denied response' => fn (): array => [RestoreAction::class, 'restore', Response::deny(), false, true],
    'replicate action with policy returning true' => fn (): array => [ReplicateAction::class, 'replicate', true, true],
    'replicate action with policy returning allowed response' => fn (): array => [ReplicateAction::class, 'replicate', Response::allow(), true],
    'replicate action with policy returning false' => fn (): array => [ReplicateAction::class, 'replicate', false, false],
    'replicate action with policy returning denied response' => fn (): array => [ReplicateAction::class, 'replicate', Response::deny(), false],
]);

class EditPostWithPartialSaving extends EditPost
{
    public array $saveEvents = [];

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Group::make([
                Section::make('Details')->key('details')->schema([
                    TextInput::make('title')->required(),
                    Section::make('Metadata')->relationship('metadata')->schema([
                        TextInput::make('seo_title'),
                    ]),
                ])->footerActions([
                    Action::make('saveSection')->action(function (Section $component): void {
                        $this->saveFormComponentOnly($component);

                        Notification::make()->title('Section saved')->success()->send();
                    }),
                ]),
                TextInput::make('content')->required(),
            ]),
        ]);
    }

    protected function beforeValidate(): void
    {
        $this->saveEvents[] = 'beforeValidate';
    }

    protected function afterValidate(): void
    {
        $this->saveEvents[] = 'afterValidate';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->saveEvents[] = 'mutate';
        $data['title'] .= ' (mutated)';

        return $data;
    }

    protected function beforeSave(): void
    {
        $this->saveEvents[] = 'beforeSave';
    }

    protected function afterSave(): void
    {
        $this->saveEvents[] = 'afterSave';
    }
}

class EditPostWithPartialSavingInFooter extends EditPostWithPartialSaving
{
    public function form(Schema $schema): Schema
    {
        $components = parent::form($schema)->getComponents();

        return $schema->components([
            Section::make('Outer')->footer(static fn (): array => $components),
        ]);
    }
}
