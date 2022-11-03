---
title: Rich editor
---

The rich editor allows you to edit and preview HTML content, as well as upload images.

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
```

![](https://user-images.githubusercontent.com/41773797/147613608-b1236c72-d5cf-40d5-aa73-70c37a5c7e4d.png)

## Toolbar buttons

You may enable / disable toolbar buttons using a range of convenient methods:

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
        'undo',
    ])
RichEditor::make('content')
    ->disableToolbarButtons([
        'attachFiles',
        'codeBlock',
    ])
RichEditor::make('content')
    ->disableAllToolbarButtons()
    ->enableToolbarButtons([
        'bold',
        'bulletList',
        'italic',
        'strike',
    ])
```

## Image uploads

You may customize how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```
