<?php

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;

uses(TestCase::class);

it('exports calculated scores with explicit options without applying options form defaults or validation', function (): void {
    $post = Post::factory()->make(['rating' => 3]);

    expect(RecipePostExporter::test(['score' => 'Score'], options: ['multiplier' => 2])->export($post))
        ->toBe(['6 points'])
        ->and(RecipePostExporter::test(['score' => 'Score'], options: ['multiplier' => 5])->export($post))
        ->toBe(['15 points'])
        ->and(RecipePostExporter::test(['score' => 'Score'])->export($post))
        ->toBe(['3 points'])
        ->and(RecipePostExporter::test(['score' => 'Score'], options: ['multiplier' => 0])->export($post))
        ->toBe(['0 points']);
});

it('exports preloaded relationships and aggregates without running `modifyQuery()` or preparing aggregates', function (): void {
    $author = User::factory()->for(Team::factory()->state(['name' => 'Editorial']))->create();
    Post::factory()->for($author, 'author')->create(['rating' => 3]);
    Post::factory()->for($author, 'author')->create(['rating' => 8]);

    $otherAuthor = User::factory()->for(Team::factory()->state(['name' => 'Research']))->create();
    Post::factory()->for($otherAuthor, 'author')->create(['rating' => 9]);

    $author = User::query()->with('team')->findOrFail($author->getKey());

    expect(RecipeAuthorExporter::test()->export($author))->toBe(['Editorial', null, null]);

    $author->loadCount('posts')->loadSum('posts', 'rating');
    $otherAuthor->load('team')->loadCount('posts')->loadSum('posts', 'rating');

    $exporter = RecipeAuthorExporter::test();

    expect($exporter->export($author))->toBe(['Editorial', '2', '11'])
        ->and($exporter->export($otherAuthor))->toBe(['Research', '1', '9']);
});

it('opts into `preventFormulaInjection()` for untrusted titles while preserving ordinary values', function (): void {
    $exporter = RecipePostExporter::test(['title' => 'Title']);

    expect($exporter->export(Post::factory()->make(['title' => '=1+1'])))->toBe(["'=1+1"])
        ->and($exporter->export(Post::factory()->make(['title' => 'Quarterly report'])))->toBe(['Quarterly report'])
        ->and($exporter->export(Post::factory()->make(['title' => '-12'])))->toBe(['-12']);
});

class RecipePostExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('score')
                ->state(static fn (Post $record, array $options): int => $record->rating * ($options['multiplier'] ?? 1))
                ->formatStateUsing(static fn (int $state): string => "{$state} points"),
            ExportColumn::make('title')
                ->preventFormulaInjection(),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            TextInput::make('multiplier')
                ->numeric()
                ->minValue(1)
                ->default(2)
                ->required(),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '';
    }
}

class RecipeAuthorExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('team.name'),
            ExportColumn::make('posts_count')->counts('posts'),
            ExportColumn::make('posts_sum_rating')->sum('posts', 'rating'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        throw new LogicException('The row helper must not prepare the query.');
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '';
    }
}
