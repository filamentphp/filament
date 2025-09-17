---
title: Markdown editor
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The markdown editor allows you to edit and preview markdown content, as well as upload images using drag and drop.

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
```

<AutoScreenshot name="forms/fields/markdown-editor/simple" alt="Markdown editor" version="4.x" />

## Security

By default, the editor outputs raw Markdown and HTML, and sends it to the backend. Attackers are able to intercept the value of the component and send a different raw HTML string to the backend. As such, it is important that when outputting the HTML from a Markdown editor, it is sanitized; otherwise your site may be exposed to Cross-Site Scripting (XSS) vulnerabilities.

When Filament outputs raw HTML from the database in components such as `TextColumn` and `TextEntry`, it sanitizes it to remove any dangerous JavaScript. However, if you are outputting the HTML from a Markdown editor in your own Blade view, this is your responsibility. One option is to use Filament's `sanitizeHtml()` helper to do this, which is the same tool we use to sanitize HTML in the components mentioned above:

```blade
{!! str($record->content)->markdown()->sanitizeHtml() !!}
```

## Customizing the toolbar buttons

You may set the toolbar buttons for the editor using the `toolbarButtons()` method. The options shown here are the defaults:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->toolbarButtons([
        ['bold', 'italic', 'strike', 'link'],
        ['heading'],
        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
        ['table', 'attachFiles'],
        ['undo', 'redo'],
    ])
```

Each nested array in the main array represents a group of buttons in the toolbar.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `toolbarButtons()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Uploading images to the editor

Images may be uploaded to the editor. They will always be uploaded to a publicly available URL with public storage permissions, since generating temporary file upload URLs is not supported in static content. You may customize where images are uploaded using configuration methods:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `fileAttachmentsDisk()` and `fileAttachmentsDirectory()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

### Validating uploaded images

You may use the `fileAttachmentsAcceptedFileTypes()` method to control a list of accepted mime types for uploaded images. By default, `image/png`, `image/jpeg`, `image/gif`, and `image/webp` are accepted:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg'])
```

You may use the `fileAttachmentsMaxSize()` method to control the maximum file size for uploaded images. The size is specified in kilobytes. By default, the maximum size is 12288 KB (12 MB):

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->fileAttachmentsMaxSize(5120) // 5 MB
```
