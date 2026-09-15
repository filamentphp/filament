<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Tenancy\HasOneThroughOwnedPosts\HasOneThroughOwnedPostResource;
use Filament\Tests\Panels\Pages\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
});

it('can create a record whose tenant ownership relationship is a `HasOneThrough`', function (): void {
    $team = Team::factory()->create();
    $author = User::factory()->create(['team_id' => $team->getKey()]);

    $this->actingAs($author);
    Filament::setTenant($team);

    HasOneThroughOwnedPostResource::observeTenancyModelCreation(Filament::getCurrentOrDefaultPanel());

    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    expect($post->teamThroughAuthor)->toBeSameModel($team);
});

it('can scope a resource to the current tenant through a `HasOneThrough` ownership relationship', function (): void {
    $team = Team::factory()->create();
    $authorInTenant = User::factory()->create(['team_id' => $team->getKey()]);
    $authorNotInTenant = User::factory()->create(['team_id' => Team::factory()->create()->getKey()]);

    $postInTenant = Post::factory()->create(['author_id' => $authorInTenant->getKey()]);
    $postNotInTenant = Post::factory()->create(['author_id' => $authorNotInTenant->getKey()]);

    $this->actingAs($authorInTenant);
    Filament::setTenant($team);

    HasOneThroughOwnedPostResource::registerTenancyModelGlobalScope(Filament::getCurrentOrDefaultPanel());

    $results = HasOneThroughOwnedPostResource::getEloquentQuery()->get();

    expect($results->pluck('id')->toArray())
        ->toContain($postInTenant->getKey())
        ->not->toContain($postNotInTenant->getKey());
});
