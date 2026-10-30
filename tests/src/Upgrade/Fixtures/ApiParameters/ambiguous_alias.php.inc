<?php

namespace Filament\Tests\Upgrade\Fixtures\AmbiguousParameterAlias;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

trait Preview
{
    public static function toPreviewHtml(array $config): ?string
    {
        return $config['title'];
    }
}

class AliasBlock extends RichContentCustomBlock
{
    use Preview {
        toPreviewHtml as renderPreview;
    }

    public static function getId(): string
    {
        return 'alias';
    }
}

class OtherBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'other';
    }

    public static function renderPreview(array $config): ?string
    {
        return strtoupper($config['title']);
    }
}

class OverrideBlock extends AliasBlock
{
    public static function renderPreview(array $config): ?string
    {
        return $config['title'] . '!';
    }
}

function preview(AliasBlock | OtherBlock $block): ?string
{
    return $block->renderPreview(config: ['title' => 'title']);
}

function result(): array
{
    return [preview(new AliasBlock), preview(new OtherBlock), preview(new OverrideBlock), AliasBlock::toPreviewHtml(config: ['title' => 'direct'])];
}
