<?php

use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with a model and attribute name', function (): void {
        $post = Post::factory()->create(['content' => 'Hello']);
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->getModel())->toBe($post);
        expect($attribute->getName())->toBe('content');
    });
});

describe('`getMergeTags()` logic', function (): void {
    it('returns `null` when no merge tags or labels are set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->getMergeTags())->toBeNull();
    });

    it('returns keys mapped to keys when `mergeTags()` is set without labels', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->mergeTags(['name' => 'John', 'email' => 'john@example.com']);

        $tags = $attribute->getMergeTags();

        expect($tags)->toBe(['name' => 'name', 'email' => 'email']);
    });

    it('merges `mergeTagLabels()` over auto-generated labels', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->mergeTags(['name' => 'John', 'email' => 'john@example.com'])
            ->mergeTagLabels(['name' => 'Full Name']);

        $tags = $attribute->getMergeTags();

        expect($tags['name'])->toBe('Full Name');
        expect($tags['email'])->toBe('email');
    });

    it('returns labels alone when only `mergeTagLabels()` is set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->mergeTagLabels(['name' => 'Full Name']);

        $tags = $attribute->getMergeTags();

        expect($tags)->toBe(['name' => 'Full Name']);
    });
});

describe('`getCustomBlocks()` logic', function (): void {
    it('returns `null` when no custom blocks are set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->getCustomBlocks())->toBeNull();
    });

    it('returns class strings from array values', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->customBlocks(['App\\Blocks\\CalloutBlock', 'App\\Blocks\\QuoteBlock']);

        $blocks = $attribute->getCustomBlocks();

        expect($blocks)->toBe(['App\\Blocks\\CalloutBlock', 'App\\Blocks\\QuoteBlock']);
    });

    it('returns string keys when blocks are keyed with config arrays', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->customBlocks([
                AttributeTestBlockA::class => ['color' => 'blue'],
                AttributeTestBlockB::class => ['style' => 'italic'],
            ]);

        $blocks = $attribute->getCustomBlocks();

        expect($blocks)->toBe([AttributeTestBlockA::class, AttributeTestBlockB::class]);
    });

    it('flattens grouped blocks into class names', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->customBlocks([
                'App\\Blocks\\AlertBlock',
                'Marketing' => [
                    'App\\Blocks\\HeroBlock',
                    'App\\Blocks\\BannerBlock',
                ],
            ]);

        $blocks = $attribute->getCustomBlocks();

        expect($blocks)->toBe([
            'App\\Blocks\\AlertBlock',
            'App\\Blocks\\HeroBlock',
            'App\\Blocks\\BannerBlock',
        ]);
    });

    it('handles mixed ungrouped data associations and groups', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->customBlocks([
                AttributeTestBlockA::class => ['color' => 'red'],
                'Marketing' => [
                    AttributeTestBlockB::class,
                ],
            ]);

        $blocks = $attribute->getCustomBlocks();

        expect($blocks)->toBe([
            AttributeTestBlockA::class,
            AttributeTestBlockB::class,
        ]);
    });

    it('flattens grouped blocks with data associations into class names', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->customBlocks([
                'Marketing' => [
                    AttributeTestBlockA::class => ['url' => '/'],
                    AttributeTestBlockB::class,
                ],
            ]);

        $blocks = $attribute->getCustomBlocks();

        expect($blocks)->toBe([
            AttributeTestBlockA::class,
            AttributeTestBlockB::class,
        ]);
    });
});

describe('`getCustomBlocksConfig()` logic', function (): void {
    it('returns an empty array when no custom blocks are set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->getCustomBlocksConfig())->toBe([]);
    });

    it('returns the raw blocks array as-is', function (): void {
        $post = Post::factory()->create();
        $blocks = [
            'App\\Blocks\\AlertBlock',
            'Marketing' => [
                'App\\Blocks\\HeroBlock',
            ],
        ];
        $attribute = RichContentAttribute::make($post, 'content')
            ->customBlocks($blocks);

        expect($attribute->getCustomBlocksConfig())->toBe($blocks);
    });
});

describe('`getTextColors()` logic', function (): void {
    it('returns default colors when none are set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        $colors = $attribute->getTextColors();

        expect($colors)->toBeArray();
        expect($colors)->not->toBeEmpty();
        expect(array_values($colors)[0])->toBeInstanceOf(TextColor::class);
    });

    it('transforms string colors to `TextColor` objects', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->textColors(['red' => '#ff0000', 'blue' => '#0000ff']);

        $colors = $attribute->getTextColors();

        expect($colors)->toHaveCount(2);
        expect($colors['red'])->toBeInstanceOf(TextColor::class);
        expect($colors['blue'])->toBeInstanceOf(TextColor::class);
    });

    it('passes through `TextColor` objects unchanged', function (): void {
        $post = Post::factory()->create();
        $tc = TextColor::make('#ff0000', 'red');
        $attribute = RichContentAttribute::make($post, 'content')
            ->textColors(['red' => $tc]);

        $colors = $attribute->getTextColors();

        expect($colors['red'])->toBe($tc);
    });
});

describe('`getFileAttachmentProvider()` logic', function (): void {
    it('returns `null` when no provider or plugins are set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->getFileAttachmentProvider())->toBeNull();
    });
});

describe('`getFileAttachmentsVisibility()` logic', function (): void {
    it('returns `null` when no visibility or provider is set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->getFileAttachmentsVisibility())->toBeNull();
    });

    it('returns explicit visibility when set', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')
            ->fileAttachmentsVisibility('private');

        expect($attribute->getFileAttachmentsVisibility())->toBe('private');
    });
});

describe('JSON mode', function (): void {
    it('defaults `isJson()` to `false`', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->isJson())->toBeFalse();
    });

    it('can set `json()`', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')->json();

        expect($attribute->isJson())->toBeTrue();
    });
});

describe('custom text colors flag', function (): void {
    it('defaults `hasCustomTextColors()` to `false`', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->hasCustomTextColors())->toBeFalse();
    });

    it('can set `customTextColors()`', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content')->customTextColors();

        expect($attribute->hasCustomTextColors())->toBeTrue();
    });
});

describe('`toHtml()` and `toText()`', function (): void {
    it('returns empty string from `toHtml()` when content is blank', function (): void {
        $post = Post::factory()->create(['content' => null]);
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->toHtml())->toBe('');
    });

    it('returns empty string from `toText()` when content is blank', function (): void {
        $post = Post::factory()->create(['content' => null]);
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->toText())->toBe('');
    });
});

describe('fluent API', function (): void {
    it('returns fluent `$this` from setters', function (): void {
        $post = Post::factory()->create();
        $attribute = RichContentAttribute::make($post, 'content');

        expect($attribute->fileAttachmentsDisk('public'))->toBe($attribute);
        expect($attribute->fileAttachmentsVisibility('public'))->toBe($attribute);
        expect($attribute->plugins([]))->toBe($attribute);
        expect($attribute->mergeTags(null))->toBe($attribute);
        expect($attribute->mergeTagLabels(null))->toBe($attribute);
        expect($attribute->mentions(null))->toBe($attribute);
        expect($attribute->customBlocks(null))->toBe($attribute);
        expect($attribute->json())->toBe($attribute);
        expect($attribute->textColors(null))->toBe($attribute);
        expect($attribute->customTextColors())->toBe($attribute);
    });
});

class AttributeTestBlockA extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'attribute-block-a';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>A</div>';
    }
}

class AttributeTestBlockB extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'attribute-block-b';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>B</div>';
    }
}
