---
title: Rich editor
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The rich editor allows you to edit and preview HTML content, as well as upload images.

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
```

<AutoScreenshot name="forms/fields/rich-editor/simple" alt="Rich editor" version="3.x" />

## Customizing the toolbar buttons

You may set the toolbar buttons for the editor using the `toolbarButtons()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->toolbarButtons([
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'underline',
        'undo',
    ])
```

Alternatively, you may disable specific buttons using the `disableToolbarButtons()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->disableToolbarButtons([
        'blockquote',
        'strike',
    ])
```

## Uploading images to the editor

You may customize how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```
