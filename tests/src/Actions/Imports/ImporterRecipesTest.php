<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Testing\TestImporter;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

uses(TestCase::class);

beforeEach(function (): void {
    Bus::fake([IndexImportedPost::class]);
});

it('associates the author matched by email instead of another existing user', function (): void {
    User::factory()->create(['email' => 'grace@example.com']);
    $author = User::factory()->create(['email' => 'ada@example.com']);

    $record = TestImporter::make(PostRecipeImporter::class)->import([
        'title' => 'Importing posts',
        'content' => 'A practical guide',
        'author' => 'ada@example.com',
    ])->assertImported()->getRecord();

    expect($record->fresh()->author->is($author))->toBeTrue();
    $this->assertDatabaseHas('posts', [
        'id' => $record->getKey(),
        'author_id' => $author->getKey(),
    ]);
    $this->assertDatabaseCount('users', 2);
});

it('rejects an unknown author without creating a parent or changing an existing association', function (): void {
    $post = Post::factory()->create(['title' => 'Importing posts', 'content' => 'Original content']);

    TestImporter::make(PostRecipeImporter::class)->import([
        'title' => $post->title,
        'content' => 'Replacement content',
        'author' => 'unknown@example.com',
    ])->assertHasErrors(['author']);

    $this->assertDatabaseHas('posts', [
        'id' => $post->getKey(),
        'author_id' => $post->author_id,
        'content' => 'Original content',
    ]);
    $this->assertDatabaseCount('posts', 1);
    $this->assertDatabaseCount('users', 1);
    Bus::assertNothingDispatched();
});

it('uses `createMissing` and `updateExisting` options to control writes and skipped rows', function (bool $createMissing, bool $updateExisting): void {
    $post = Post::factory()->create(['title' => 'Existing post', 'content' => 'Original content']);
    $author = User::factory()->create();
    $importer = TestImporter::make(PostRecipeImporter::class, options: [
        'createMissing' => $createMissing,
        'updateExisting' => $updateExisting,
    ]);

    $importer->import([
        'title' => 'Existing post',
        'content' => 'Replacement content',
        'author' => $author->email,
    ]);

    if ($updateExisting) {
        $importer->assertImported();
        expect($importer->getRecord()->is($post))->toBeTrue();
    } else {
        $importer->assertSkipped();
    }

    $this->assertDatabaseHas('posts', [
        'id' => $post->getKey(),
        'content' => $updateExisting ? 'Replacement content' : 'Original content',
        'author_id' => $updateExisting ? $author->getKey() : $post->author_id,
    ]);

    $importer->import([
        'title' => 'New post',
        'content' => 'New content',
        'author' => $author->email,
    ]);

    if ($createMissing) {
        $record = $importer->assertImported()->getRecord();
        $this->assertDatabaseHas('posts', [
            'id' => $record->getKey(),
            'title' => 'New post',
            'content' => 'New content',
            'author_id' => $author->getKey(),
        ]);
    } else {
        $importer->assertSkipped();
        $this->assertDatabaseMissing('posts', ['title' => 'New post']);
    }

    $this->assertDatabaseCount('posts', $createMissing ? 2 : 1);
    Bus::assertDispatchedTimes(IndexImportedPost::class, (int) $createMissing + (int) $updateExisting);
})->with([
    'create only' => [true, false],
    'update only' => [false, true],
    'create and update' => [true, true],
    'skip all' => [false, false],
]);

it('dispatches the application indexing job for the saved post but not for invalid content', function (): void {
    $author = User::factory()->create();
    $importer = TestImporter::make(PostRecipeImporter::class);

    $importer->import([
        'title' => 'Importing posts',
        'content' => '',
        'author' => $author->email,
    ])->assertHasErrors(['content' => 'required']);

    $this->assertDatabaseCount('posts', 0);
    Bus::assertNothingDispatched();

    $record = $importer->import([
        'title' => 'Importing posts',
        'content' => 'A practical guide',
        'author' => $author->email,
    ])->assertImported()->getRecord();

    $this->assertDatabaseHas('posts', [
        'id' => $record->getKey(),
        'content' => 'A practical guide',
    ]);
    Bus::assertDispatched(IndexImportedPost::class, static fn (IndexImportedPost $job): bool => $job->postId === $record->getKey());
    Bus::assertDispatchedTimes(IndexImportedPost::class, 1);
});

class PostRecipeImporter extends Importer
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')->rules(['required']),
            ImportColumn::make('content')->rules(['required']),
            ImportColumn::make('author')
                ->rules(['required'])
                ->relationship(resolveUsing: static fn (string $state): ?User => User::query()->where('email', $state)->first()),
        ];
    }

    public function resolveRecord(): ?Post
    {
        $post = Post::query()->firstOrNew(['title' => $this->data['title']]);

        if ($post->exists) {
            return ($this->options['updateExisting'] ?? true) ? $post : null;
        }

        return ($this->options['createMissing'] ?? true) ? $post : null;
    }

    protected function afterSave(): void
    {
        Bus::dispatch(new IndexImportedPost($this->record->getKey()));
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

class IndexImportedPost implements ShouldQueue
{
    public function __construct(public int $postId) {}
}
