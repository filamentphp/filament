<?php

namespace Filament\Tests\Upgrade\Fixtures\ParameterCollisions;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class Collision extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'collision';
    }

    public static function toPreviewHtml(array $config, array $configuration = []): ?string
    {
        return $config['title'] . ($configuration['suffix'] ?? '!');
    }

    public static function toHtml(array $config, array $data): ?string
    {
        $configuration = ['title' => 'independent'];

        return $config['title'] . '/' . $configuration['title'];
    }
}

class CleanBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'clean';
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return $config['title'];
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return $config['title'];
    }
}

function render(RichContentCustomBlock $block): ?string
{
    return $block->toHtml(config: ['title' => 'original'], data: []);
}

function preview(RichContentCustomBlock $block): ?string
{
    return $block->toPreviewHtml(config: ['title' => 'preview']);
}

function result(): array
{
    return [render(new Collision), render(new CleanBlock), preview(new Collision), preview(new CleanBlock), Collision::toPreviewHtml(config: ['title' => 'Keep'], configuration: ['suffix' => '!'])];
}
