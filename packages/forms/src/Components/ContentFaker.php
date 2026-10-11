<?php

namespace Filament\Forms\Components;

use Faker\Generator;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class ContentFaker
{
    public function __construct(
        protected Generator $faker,
    ) {}

    public static function make(Generator $faker): static
    {
        return app(static::class, ['faker' => $faker]);
    }

    public function article(int $depth = 1): static
    {
        if (($depth < 1) || ($depth > 5)) {
            throw new InvalidArgumentException('Article depth must be between 1 and 5.');
        }

        $this->addArticleIntroduction();
        $this->addArticleSections(level: 2, depth: $depth, isRoot: true);

        return $this;
    }

    abstract protected function addArticleIntroduction(): void;

    protected function addArticleSections(int $level, int $depth, bool $isRoot = false): void
    {
        $sectionCount = $this->faker->numberBetween($isRoot ? 2 : 1, $isRoot ? 4 : 2);

        for ($section = 0; $section < $sectionCount; $section++) {
            $this->addArticleHeading($level);
            $this->addArticleParagraphs(
                count: $this->faker->numberBetween(1, 3),
                links: $this->faker->boolean(30),
                bold: $this->faker->boolean(40),
                italic: $this->faker->boolean(20),
            );

            if ($depth > 1) {
                $this->addArticleSections(level: $level + 1, depth: $depth - 1);
            }
        }
    }

    abstract protected function addArticleHeading(int $level): void;

    abstract protected function addArticleParagraphs(int $count, bool $links, bool $bold, bool $italic): void;

    protected function makeHeadingText(): string
    {
        return Str::title($this->faker->words($this->faker->numberBetween(3, 8), true));
    }

    protected function makeParagraphText(bool $sentence = false): string
    {
        return rtrim($sentence ? $this->faker->sentence() : $this->faker->paragraph(), '.');
    }

    protected function makeImageUrl(int $width, int $height): string
    {
        return "https://picsum.photos/seed/{$this->faker->uuid()}/{$width}/{$height}";
    }

    /**
     * @param  ?array<string>  $protocols
     */
    protected function makeLinkUrl(?array $protocols = null): string
    {
        if ($protocols === null) {
            return $this->faker->url();
        }

        if ($protocols === []) {
            return '/' . $this->faker->slug();
        }

        $protocol = $this->faker->randomElement($protocols);

        return match ($protocol) {
            'http', 'https', 'ftp', 'ftps' => "{$protocol}://{$this->faker->domainName()}/{$this->faker->slug()}",
            'mailto' => "mailto:{$this->faker->safeEmail()}",
            'tel', 'callto', 'sms' => "{$protocol}:{$this->faker->numerify('+1##########')}",
            'cid' => "cid:{$this->faker->uuid()}",
            'xmpp' => "xmpp:{$this->faker->userName()}@{$this->faker->domainName()}",
            default => "{$protocol}:{$this->faker->slug()}",
        };
    }
}
