<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\PostCategories\PostCategoryResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Shop\OrderInvoiceResource;
use Filament\Tests\Fixtures\Resources\Shop\OrderResource;
use Filament\Tests\Fixtures\Resources\Shop\Products\ProductResource;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

uses(TestCase::class);

it('can retrieve the model query using `getEloquentQuery()`', function (): void {
    expect(PostResource::getEloquentQuery())
        ->toBeInstanceOf(Builder::class)
        ->getModel()->toBeInstanceOf(Post::class);
});

describe('slugs', function (): void {
    it('can generate a slug based on the model name', function (): void {
        expect(PostResource::getSlug())
            ->toBe('posts');
    });

    it('can generate a slug based on the multi-word model name', function (): void {
        expect(PostCategoryResource::getSlug())
            ->toBe('post-categories');
    });

    it('can generate a nested slug based on the model name', function (): void {
        expect(ProductResource::getSlug())
            ->toBe('shop/products');
    });

    it('can generate a nested slug based on the model name with a resource outside of its directory', function (): void {
        expect(OrderResource::getSlug())
            ->toBe('shop/orders');
    });

    it('can generate a nested slug based on the multi-word model name', function (): void {
        expect(OrderInvoiceResource::getSlug())
            ->toBe('shop/order-invoices');
    });
});

describe('model labels', function (): void {
    it('can generate a label based on the model name', function (): void {
        expect(PostResource::getModelLabel())
            ->toBe('post');
    });

    it('can generate a label based on the multi-word model name', function (): void {
        expect(PostCategoryResource::getModelLabel())
            ->toBe('post category');
    });

    it('can generate a plural label based on the model name and locale', function (): void {
        $originalLocale = app()->getLocale();

        app()->setLocale('en');
        expect(PostResource::getPluralModelLabel())
            ->toBe('posts');

        app()->setLocale('id');
        expect(PostResource::getPluralModelLabel())
            ->toBe('post');

        app()->setLocale($originalLocale);
    });

    it('can generate a plural label based on the multi-word model name and locale', function (): void {
        $originalLocale = app()->getLocale();

        app()->setLocale('en');
        expect(PostCategoryResource::getPluralModelLabel())
            ->toBe('post categories');

        app()->setLocale('id');
        expect(PostCategoryResource::getPluralModelLabel())
            ->toBe('post category');

        app()->setLocale($originalLocale);
    });

});

describe('records', function (): void {
    it('can retrieve a record title using `getRecordTitle()`', function (): void {
        $post = Post::factory()->create();

        expect(PostResource::getRecordTitle($post))
            ->toBe($post->title);
    });

    it('can resolve a record using `resolveRecordRouteBinding()`', function (): void {
        $post = Post::factory()->create();

        expect(PostResource::resolveRecordRouteBinding($post->getKey()))
            ->toBeSameModel($post);
    });
});

describe('URLs', function (): void {
    it('can retrieve a page URL using `getUrl()`', function (): void {
        $post = Post::factory()->create();
        $resourceSlug = PostResource::getSlug();

        expect(PostResource::getUrl('create'))
            ->toContain($resourceSlug)
            ->toContain('create');
        expect(PostResource::getUrl('edit', ['record' => $post]))
            ->toContain($resourceSlug)
            ->toContain(strval($post->getRouteKey()));
        expect(PostResource::getUrl('index'))->toContain($resourceSlug);
        expect(PostResource::getUrl('view', ['record' => $post]))
            ->toContain($resourceSlug)
            ->toContain(strval($post->getRouteKey()));
    });

    it('can retrieve a page URL from its model using `getResourceUrl()`', function (): void {
        $post = Post::factory()->create();

        expect(Filament::getResourceUrl($post, 'edit'))
            ->toEndWith("/posts/{$post->getKey()}/edit");
        expect(Filament::getResourceUrl($post, 'view'))
            ->toEndWith("/posts/{$post->getKey()}");
        expect(Filament::getResourceUrl(Post::class, 'view', ['record' => $post]))
            ->toEndWith("/posts/{$post->getKey()}");
        expect(Filament::getResourceUrl(Post::class))
            ->toEndWith('/posts');
        expect(Filament::getResourceUrl($post))
            ->toEndWith('/posts');
    });
});

describe('authorization', function (): void {
    it('uses overridden authorization responses for boolean checks and authorization', function (): void {
        $resource = new class extends PostResource
        {
            public static function getCreateAuthorizationResponse(): Response
            {
                return Response::deny();
            }

            public static function getEditAuthorizationResponse(Model $record): Response
            {
                return $record->is_published ? parent::getEditAuthorizationResponse($record) : Response::deny();
            }

            public static function getViewAuthorizationResponse(Model $record): Response
            {
                return $record->is_published ? parent::getViewAuthorizationResponse($record) : Response::deny();
            }
        };

        $publishedPost = Post::factory()->create(['is_published' => true]);
        $unpublishedPost = Post::factory()->create(['is_published' => false]);

        expect($resource::getCreateAuthorizationResponse()->allowed())->toBeFalse();
        expect($resource::canCreate())->toBeFalse();
        expect(fn () => $resource::authorizeCreate())->toThrow(AuthorizationException::class);

        expect($resource::getEditAuthorizationResponse($publishedPost)->allowed())->toBeTrue();
        expect($resource::canEdit($publishedPost))->toBeTrue();
        expect($resource::getEditAuthorizationResponse($unpublishedPost)->allowed())->toBeFalse();
        expect($resource::canEdit($unpublishedPost))->toBeFalse();
        expect(fn () => $resource::authorizeEdit($unpublishedPost))->toThrow(AuthorizationException::class);

        expect($resource::getViewAuthorizationResponse($publishedPost)->allowed())->toBeTrue();
        expect($resource::canView($publishedPost))->toBeTrue();
        expect($resource::getViewAuthorizationResponse($unpublishedPost)->allowed())->toBeFalse();
        expect($resource::canView($unpublishedPost))->toBeFalse();
        expect(fn () => $resource::authorizeView($unpublishedPost))->toThrow(AuthorizationException::class);
    });

    it('preserves the policy response without consulting an overridden `canEdit()` method', function (Response $response): void {
        app()->bind(TicketPolicy::class . '::update', fn (): Response => $response);

        $resource = new class extends TicketResource
        {
            public static function canEdit(Model $record): bool
            {
                return false;
            }
        };

        expect($resource::getEditAuthorizationResponse(Ticket::factory()->create()))
            ->toBe($response);

        app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
    })->with([
        'allowed' => [Response::allow()],
        'denied as not found' => [Response::denyAsNotFound()],
    ]);
});
