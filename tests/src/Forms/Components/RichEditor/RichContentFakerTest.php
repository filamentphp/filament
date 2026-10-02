<?php

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Filament\Forms\Components\MarkdownEditor\MarkdownFaker;
use Filament\Forms\Components\RichEditor\Contracts\CanGenerateFakeConfiguration;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentFaker;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\FormsServiceProvider;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

uses(TestCase::class);

it('registers the Filament content provider with Faker', function (): void {
    expect(fake()->filamentRichContent())->toBeInstanceOf(RichContentFaker::class)
        ->and(fake()->filamentMarkdown())->toBeInstanceOf(MarkdownFaker::class);
});

it('registers with locale-specific Faker generators that were resolved before the service provider', function (string $binding): void {
    $container = new Container;
    $container->singleton($binding, static fn (): Generator => FakerFactory::create());

    $faker = $container->make($binding);

    $serviceProvider = (new ReflectionClass(FormsServiceProvider::class))->newInstanceWithoutConstructor();
    (new ReflectionProperty(ServiceProvider::class, 'app'))->setValue($serviceProvider, $container);
    $serviceProvider->packageRegistered();

    expect($faker->filamentRichContent())->toBeInstanceOf(RichContentFaker::class)
        ->and($faker->filamentMarkdown())->toBeInstanceOf(MarkdownFaker::class);
})->with([
    Generator::class,
    Generator::class . ':en_US',
    Generator::class . ':fr_FR',
]);

it('accepts a `RichContentAttribute` from the Faker provider', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')->json();

    expect(fake()->filamentRichContent($attribute)->paragraphs()->toValue())->toBeArray();
});

it('serializes an empty builder as a valid document', function (): void {
    $expectedDocument = [
        'type' => 'doc',
        'content' => [['type' => 'paragraph']],
    ];

    expect(fake()->filamentRichContent()->toArray())->toBe($expectedDocument)
        ->and(fake()->filamentRichContent()->paragraphs(0)->toArray())->toBe($expectedDocument);

    $attribute = RichContentAttribute::make(new Post, 'content')->json();

    expect(fake()->filamentRichContent($attribute)->toValue())->toBe($expectedDocument);
});

it('generates deterministic rich content using the Faker seed', function (): void {
    fake()->seed(1234);

    $firstDocument = fake()->filamentRichContent()
        ->article(depth: 2)
        ->toArray();

    fake()->seed(1234);

    $secondDocument = fake()->filamentRichContent()
        ->article(depth: 2)
        ->toArray();

    expect($secondDocument)->toBe($firstDocument);
});

it('generates articles to the configured depth', function (): void {
    fake()->seed(5678);

    $content = fake()->filamentRichContent()
        ->article(depth: 3);

    $document = $content->toArray();

    $headingLevels = collect($document['content'])
        ->where('type', 'heading')
        ->pluck('attrs.level')
        ->unique()
        ->sort()
        ->values()
        ->all();

    expect($document['content'][0]['type'])->toBe('lead')
        ->and($headingLevels)->toBe([2, 3, 4])
        ->and($content->toHtml())
        ->toContain('<h2>')
        ->toContain('<h3>')
        ->toContain('<h4>');
});

it('rejects article depths that cannot map to heading levels', function (int $depth): void {
    fake()->filamentRichContent()->article(depth: $depth);
})->with([0, 6])->throws(InvalidArgumentException::class);

it('generates multiple paragraphs inside a lead', function (): void {
    $document = fake()->filamentRichContent()
        ->lead(paragraphs: 2)
        ->toArray();

    expect($document['content'])->toHaveCount(1)
        ->and($document['content'][0]['type'])->toBe('lead')
        ->and($document['content'][0]['content'])->toHaveCount(2)
        ->and(collect($document['content'][0]['content'])->pluck('type')->unique()->all())->toBe(['paragraph']);
});

it('generates all supported core block nodes', function (): void {
    $content = fake()->filamentRichContent()
        ->heading()
        ->paragraphs(2)
        ->bulletList()
        ->orderedList()
        ->blockquote()
        ->codeBlock(language: 'php', code: '<?php echo true;')
        ->horizontalRule()
        ->table(columns: 2, rows: 2)
        ->details()
        ->grid([1, 2])
        ->image('https://example.com/image.jpg')
        ->fileAttachment('attachments/image.jpg')
        ->customBlock('call-to-action', ['style' => 'primary'])
        ->hardBreak()
        ->mergeTag('name');

    $document = $content->toArray();

    expect(collect($document['content'])->pluck('type')->all())->toBe([
        'heading',
        'paragraph',
        'paragraph',
        'bulletList',
        'orderedList',
        'blockquote',
        'codeBlock',
        'horizontalRule',
        'table',
        'details',
        'grid',
        'paragraph',
        'paragraph',
        'customBlock',
    ])->and($document['content'][8]['content'])->toHaveCount(2)
        ->and($document['content'][8]['content'][0]['content'][0]['type'])->toBe('tableHeader')
        ->and($document['content'][8]['content'][1]['content'][0]['type'])->toBe('tableCell')
        ->and($document['content'][10]['attrs']['data-cols'])->toBe(3)
        ->and($document['content'][11]['content'][0]['type'])->toBe('image')
        ->and($document['content'][12]['content'][0]['type'])->toBe('image');

    expect(json_encode($document))
        ->toContain('"type":"hardBreak"')
        ->toContain('"type":"mergeTag"');

    $roundTrippedDocument = RichContentRenderer::make($content->toHtml())
        ->getEditor()
        ->getDocument();

    expect(json_encode($roundTrippedDocument))
        ->toContain('"type":"table"')
        ->toContain('"type":"details"')
        ->toContain('"type":"grid"')
        ->toContain('"type":"image"')
        ->toContain('"type":"customBlock"')
        ->toContain('"type":"hardBreak"')
        ->toContain('"type":"mergeTag"');
});

it('generates an empty code block without an invalid empty text node', function (): void {
    $content = fake()->filamentRichContent()
        ->paragraphs()
        ->codeBlock(code: '')
        ->paragraphs();

    $document = $content->toArray();

    expect($document['content'][1])
        ->toMatchArray(['type' => 'codeBlock'])
        ->not->toHaveKey('content');

    $roundTrippedDocument = RichContentRenderer::make($content->toHtml())
        ->getEditor()
        ->getDocument();

    expect(collect($roundTrippedDocument['content'])->pluck('type')->all())
        ->toBe(['paragraph', 'codeBlock', 'paragraph']);
});

it('generates deterministic image URLs using an available image service', function (): void {
    fake()->seed(2345);

    $firstImage = fake()->filamentRichContent()
        ->image(width: 640, height: 480)
        ->toArray()['content'][0]['content'][0];

    fake()->seed(2345);

    $secondImage = fake()->filamentRichContent()
        ->image(width: 640, height: 480)
        ->toArray()['content'][0]['content'][0];

    expect($firstImage['attrs']['src'])
        ->toBe($secondImage['attrs']['src'])
        ->toStartWith('https://picsum.photos/seed/')
        ->toEndWith('/640/480');
});

it('generates requested inline formatting', function (): void {
    $document = fake()->filamentRichContent()
        ->paragraphs(
            links: true,
            bold: true,
            italic: true,
            underline: true,
            strike: true,
            subscript: true,
            superscript: true,
            code: true,
            small: true,
            highlight: true,
        )
        ->toArray();

    $marks = collect($document['content'][0]['content'])
        ->pluck('marks')
        ->flatten(1)
        ->pluck('type')
        ->filter()
        ->values()
        ->all();

    expect($marks)->toBe([
        'bold',
        'italic',
        'underline',
        'strike',
        'subscript',
        'superscript',
        'code',
        'small',
        'highlight',
        'link',
    ]);
});

it('uses configured merge tags and inserts them inside paragraphs', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->mergeTags([
            'first_name' => 'Taylor',
            'company' => 'Filament',
        ]);

    $document = fake()->filamentRichContent($attribute)
        ->paragraphs(2)
        ->mergeTags(2)
        ->toArray();

    expect(collect($document['content'])->pluck('type')->unique()->all())->toBe(['paragraph']);

    $mergeTags = collect($document['content'])
        ->pluck('content')
        ->flatten(1)
        ->where('type', 'mergeTag');

    expect($mergeTags)->toHaveCount(2)
        ->and($mergeTags->pluck('attrs.id')->diff(['first_name', 'company']))->toBeEmpty();
});

it('uses configured mention providers and inserts mentions inside paragraphs', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->mentions([
            MentionProvider::make('@')->items([
                '1' => 'Taylor',
                '2' => 'Jordan',
            ]),
        ]);

    $document = fake()->filamentRichContent($attribute)
        ->paragraphs()
        ->mention()
        ->toArray();

    $mention = collect($document['content'][0]['content'])->firstWhere('type', 'mention');

    expect($mention)->not->toBeNull()
        ->and($mention['attrs']['char'])->toBe('@')
        ->and(['Taylor', 'Jordan'])->toContain($mention['attrs']['label']);
});

it('uses configured mention providers that have items available', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->mentions([
            MentionProvider::make('@')->items([]),
            MentionProvider::make('#')->items([
                'filament' => 'Filament',
            ]),
        ]);

    $document = fake()->filamentRichContent($attribute)
        ->mention()
        ->toArray();

    $mention = collect($document['content'][0]['content'])->firstWhere('type', 'mention');

    expect($mention['attrs'])->toMatchArray([
        'id' => 'filament',
        'label' => 'Filament',
        'char' => '#',
    ]);
});

it('requires a trigger character for an explicit mention ID when multiple providers are configured', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->mentions([
            MentionProvider::make('@')->items(['1' => 'Taylor']),
            MentionProvider::make('#')->items(['filament' => 'Filament']),
        ]);

    expect(fn () => fake()->filamentRichContent($attribute)->mention(id: '1'))
        ->toThrow(InvalidArgumentException::class, 'A mention trigger character must be specified when generating an explicit mention ID with multiple providers.');
});

it('creates a paragraph when inserting inline content into an empty document', function (): void {
    $document = fake()->filamentRichContent()
        ->mention(id: '1', char: '@')
        ->toArray();

    expect($document['content'])->toHaveCount(1)
        ->and($document['content'][0]['type'])->toBe('paragraph')
        ->and(collect($document['content'][0]['content'])->pluck('type'))->toContain('mention');
});

it('preserves surrounding whitespace when inserting another inline node', function (): void {
    $document = fake()->filamentRichContent()
        ->block([
            'type' => 'paragraph',
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'alpha beta ',
                ],
                [
                    'type' => 'mention',
                    'attrs' => [
                        'id' => '1',
                        'label' => 'One',
                        'char' => '@',
                    ],
                ],
            ],
        ])
        ->mention(id: '2', char: '@')
        ->toArray();

    $inlineContent = $document['content'][0]['content'];

    expect($inlineContent)->toHaveCount(4)
        ->and($inlineContent[0])->toMatchArray(['type' => 'text', 'text' => 'alpha '])
        ->and($inlineContent[1]['type'])->toBe('mention')
        ->and($inlineContent[1]['attrs']['id'])->toBe('2')
        ->and($inlineContent[2])->toMatchArray(['type' => 'text', 'text' => ' beta '])
        ->and($inlineContent[3]['type'])->toBe('mention')
        ->and($inlineContent[3]['attrs']['id'])->toBe('1');
});

it('separates appended inline nodes when no text can be split', function (): void {
    $document = fake()->filamentRichContent()
        ->paragraphs()
        ->textColor('red')
        ->mention(id: '1')
        ->mention(id: '2')
        ->toArray();

    $inlineContent = $document['content'][0]['content'];
    $firstMentionIndex = collect($inlineContent)->search(
        static fn (array $node): bool => (($node['type'] ?? null) === 'mention') && (($node['attrs']['id'] ?? null) === '1'),
    );
    $secondMentionIndex = collect($inlineContent)->search(
        static fn (array $node): bool => (($node['type'] ?? null) === 'mention') && (($node['attrs']['id'] ?? null) === '2'),
    );

    expect($firstMentionIndex)->toBeInt()
        ->and($secondMentionIndex)->toBeInt()
        ->and($inlineContent[$firstMentionIndex - 1])->toMatchArray(['type' => 'text', 'text' => '. '])
        ->and($inlineContent[$secondMentionIndex - 1])->toMatchArray(['type' => 'text', 'text' => ' ']);
});

it('inserts inline nodes into an empty paragraph', function (): void {
    $mentionDocument = fake()->filamentRichContent()
        ->block(['type' => 'paragraph'])
        ->mention(id: '1')
        ->toArray();

    $hardBreakDocument = fake()->filamentRichContent()
        ->block(['type' => 'paragraph'])
        ->hardBreak()
        ->toArray();

    expect($mentionDocument['content'][0]['content'])->toBe([[
        'type' => 'mention',
        'attrs' => [
            'id' => '1',
            'label' => '1',
            'char' => '@',
        ],
    ]])->and($hardBreakDocument['content'][0]['content'])->toBe([[
        'type' => 'hardBreak',
    ]]);
});

it('does not duplicate existing whitespace before an appended inline node', function (): void {
    $document = fake()->filamentRichContent()
        ->block([
            'type' => 'paragraph',
            'content' => [[
                'type' => 'text',
                'text' => 'alpha ',
            ]],
        ])
        ->mention(id: '1')
        ->toArray();

    expect($document['content'][0]['content'])->toHaveCount(2)
        ->and($document['content'][0]['content'][0]['text'])->toBe('alpha ')
        ->and($document['content'][0]['content'][1]['type'])->toBe('mention');
});

it('applies configured text colors and text alignment', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->textColors(['brand' => '#123456']);

    $document = fake()->filamentRichContent($attribute)
        ->paragraphs()
        ->textColor()
        ->textAlignment('justify')
        ->toArray();

    $marks = collect($document['content'][0]['content'])
        ->pluck('marks')
        ->flatten(1);

    expect($marks->firstWhere('type', 'textColor')['attrs']['data-color'])->toBe('brand')
        ->and($document['content'][0]['attrs']['textAlign'])->toBe('justify');
});

it('replaces an existing text color and does not combine text colors with code', function (): void {
    $document = fake()->filamentRichContent()
        ->paragraphs(code: true)
        ->textColor('red')
        ->textColor('blue')
        ->toArray();

    $textNodes = collect($document['content'][0]['content']);
    $codeNode = $textNodes->first(fn (array $node): bool => collect($node['marks'] ?? [])->contains('type', 'code'));
    $coloredNode = $textNodes->first(fn (array $node): bool => collect($node['marks'] ?? [])->contains('type', 'textColor'));

    expect($codeNode['marks'])->toBe([['type' => 'code']])
        ->and($coloredNode['marks'])->toBe([[
            'type' => 'textColor',
            'attrs' => ['data-color' => 'blue'],
        ]]);
});

it('applies a text color to eligible paragraph text when the document contains other nodes', function (): void {
    fake()->seed(2);

    $document = fake()->filamentRichContent()
        ->block([
            'type' => 'paragraph',
            'content' => [[
                'type' => 'image',
                'attrs' => ['src' => 'https://example.com/image.jpg'],
            ]],
        ])
        ->block([
            'type' => 'codeBlock',
            'content' => [[
                'type' => 'text',
                'text' => 'Unmarkable code',
            ]],
        ])
        ->block([
            'type' => 'paragraph',
            'content' => [[
                'type' => 'text',
                'text' => 'Eligible text',
            ]],
        ])
        ->textColor('red')
        ->toArray();

    expect($document['content'][1]['content'][0])->not->toHaveKey('marks')
        ->and($document['content'][2]['content'][0]['marks'])->toBe([[
            'type' => 'textColor',
            'attrs' => ['data-color' => 'red'],
        ]]);
});

it('rejects automatic text color selection from an empty palette while allowing an explicit color', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->textColors([]);

    expect(fn () => fake()->filamentRichContent($attribute)->textColor())
        ->toThrow(LogicException::class, 'No text colors are configured on the rich content attribute.');

    $document = fake()->filamentRichContent($attribute)
        ->textColor('#123456')
        ->toArray();

    expect($document['content'][0]['content'][0]['marks'])->toBe([[
        'type' => 'textColor',
        'attrs' => ['data-color' => '#123456'],
    ]]);
});

it('generates links using the protocols configured on the rich content attribute', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->linkProtocols(['webcal']);

    $document = fake()->filamentRichContent($attribute)
        ->paragraphs(links: true)
        ->toArray();

    $link = collect($document['content'][0]['content'])
        ->pluck('marks')
        ->flatten(1)
        ->firstWhere('type', 'link');

    expect($link['attrs']['href'])->toStartWith('webcal:');
});

it('generates configured custom blocks using their fake configuration contract', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->customBlocks([
            UnfakeableRichContentBlock::class,
            FakeableRichContentBlock::class,
        ]);

    $content = fake()->filamentRichContent($attribute)
        ->customBlock();

    $block = $content->toArray()['content'][0];

    expect($block['attrs']['id'])->toBe('fakeable')
        ->and($block['attrs']['config']['message'])->toBeString()
        ->and($content->toHtml())
        ->toContain('data-type="customBlock"')
        ->toContain('data-id="fakeable"')
        ->not->toContain('fakeable-block')
        ->and($content->toRenderedHtml())->toContain('fakeable-block');
});

it('rejects automatic custom block generation when no block opts in', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->customBlocks([UnfakeableRichContentBlock::class]);

    fake()->filamentRichContent($attribute)->customBlock();
})->throws(LogicException::class, 'No registered custom blocks can generate fake configuration.');

it('generates custom plugin block and inline nodes', function (): void {
    $document = fake()->filamentRichContent()
        ->paragraphs()
        ->block(['type' => 'pluginBlock', 'attrs' => ['variant' => 'info']])
        ->inline(['type' => 'pluginInline', 'attrs' => ['id' => 'example']])
        ->toArray();

    expect(collect($document['content'])->pluck('type'))->toContain('pluginBlock')
        ->and(collect($document['content'][0]['content'])->pluck('type'))->toContain('pluginInline');
});

it('infers the stored value format from the rich content attribute', function (): void {
    $htmlAttribute = RichContentAttribute::make(new Post, 'content');
    $jsonAttribute = RichContentAttribute::make(new Post, 'content')->json();

    expect(fake()->filamentRichContent($htmlAttribute)->paragraphs()->toValue())->toBeString()
        ->and(fake()->filamentRichContent($jsonAttribute)->paragraphs()->toValue())->toBeArray();
});

it('preserves special nodes in HTML stored values', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->mergeTags(['name' => 'Taylor'])
        ->mentions([
            MentionProvider::make('@')->items(['1' => 'Taylor']),
            MentionProvider::make('#')->items(['1' => 'Filament']),
        ])
        ->customBlocks([
            'Editorial' => [FakeableRichContentBlock::class],
        ]);

    $html = fake()->filamentRichContent($attribute)
        ->paragraphs()
        ->mergeTag()
        ->mention(id: '1', char: '@')
        ->mention(id: '1', char: '#')
        ->customBlock()
        ->toValue();

    expect($html)->toBeString()
        ->toContain('data-type="mergeTag"')
        ->toContain('data-id="name"')
        ->toContain('data-type="mention"')
        ->toContain('data-id="1"')
        ->toContain('data-char="@"')
        ->toContain('data-char="#"')
        ->toContain('data-type="customBlock"')
        ->toContain('data-id="fakeable"')
        ->not->toContain('fakeable-block');

    $roundTrippedDocument = $attribute->getRenderer()
        ->content($html)
        ->getEditor()
        ->getDocument();

    expect(json_encode($roundTrippedDocument))
        ->toContain('"type":"mergeTag"')
        ->toContain('"type":"mention"')
        ->toContain('"char":"@"')
        ->toContain('"char":"#"')
        ->toContain('"type":"customBlock"');
});

it('preserves configured special nodes in JSON stored values', function (): void {
    $attribute = RichContentAttribute::make(new Post, 'content')
        ->json()
        ->mergeTags(['name' => 'Taylor'])
        ->mentions([
            MentionProvider::make('@')->items(['1' => 'Taylor']),
            MentionProvider::make('#')->items(['1' => 'Filament']),
        ])
        ->customBlocks([
            'Editorial' => [FakeableRichContentBlock::class],
        ]);

    $document = fake()->filamentRichContent($attribute)
        ->paragraphs()
        ->mergeTag()
        ->mention(id: '1', char: '@')
        ->mention(id: '1', char: '#')
        ->customBlock()
        ->toValue();

    expect($document)->toBeArray();

    $encodedDocument = json_encode($document);

    expect($encodedDocument)
        ->toContain('"type":"mergeTag"')
        ->toContain('"type":"mention"')
        ->toContain('"char":"@"')
        ->toContain('"char":"#"')
        ->toContain('"type":"customBlock"')
        ->toContain('"id":"fakeable"');
});

it('renders generated content as HTML and plain text', function (): void {
    fake()->seed(9012);

    $content = fake()->filamentRichContent()
        ->heading(level: 2)
        ->paragraphs();

    expect($content->toHtml())
        ->toContain('<h2>')
        ->toContain('<p>')
        ->and($content->toText())->not->toBeEmpty();
});

class FakeableRichContentBlock extends RichContentCustomBlock implements CanGenerateFakeConfiguration
{
    public static function getId(): string
    {
        return 'fakeable';
    }

    public static function generateFakeConfiguration(Generator $faker): array
    {
        return ['message' => $faker->sentence()];
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div class="fakeable-block">' . e($config['message']) . '</div>';
    }
}

class UnfakeableRichContentBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'unfakeable';
    }
}
