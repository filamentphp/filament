---
title: Rich editor
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The rich editor allows you to edit and preview HTML content, as well as upload images. It uses [TipTap](https://tiptap.dev) as the underlying editor.

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
```

<AutoScreenshot name="forms/fields/rich-editor/simple" alt="Rich editor" version="4.x" />

## Storing content as JSON

By default, the rich editor stores content as HTML. If you would like to store the content as JSON instead, you can use the `json()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->json()
```

The JSON is in [TipTap's](https://tiptap.dev) format, which is a structured representation of the content.

If you're saving the JSON content using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    // ...
}
```

## Customizing the toolbar buttons

You may set the toolbar buttons for the editor using the `toolbarButtons()` method. The options shown here are the defaults:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->toolbarButtons([
        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
        ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
        ['table', 'attachFiles'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
        ['undo', 'redo'],
    ])
```

Each nested array in the main array represents a group of buttons in the toolbar.

Additional tools available in the toolbar include:

- `h1` - Applies the "h1" tag to the text.
- `alignJustify` - Justifies the text.
- `clearFormatting` - Clears all formatting from the selected text.
- `details` - Inserts a `<details>` tag, which allows users to create collapsible sections in their content.
- `grid` - Inserts a grid layout into the editor, allowing users to create responsive columns of content.
- `gridDelete` - Deletes the current grid layout.
- `highlight` - Highlights the selected text with a `<mark>` tag around it.
- `horizontalRule` - Inserts a horizontal rule.
- `lead` - Applies a `lead` class around the text, which is typically used for the first paragraph of an article.
- `small` - Applies the `<small>` tag to the text, which is typically used for small print or disclaimers.
- `code` - Format the selected text as inline code.
- `textColor` - Changes the [text color](#customizing-text-colors) of the selected text.
- `table` - Creates a table in the editor with a default layout of 3 columns and 2 rows, with the first row configured as a header row.
- `tableAddColumnBefore` - Adds a new column before the current column.
- `tableAddColumnAfter` - Adds a new column after the current column.
- `tableDeleteColumn` - Deletes the current column.
- `tableAddRowBefore` - Adds a new row above the current row.
- `tableAddRowAfter` - Adds a new row below the current row.
- `tableDeleteRow` - Deletes the current row.
- `tableMergeCells` - Merges the selected cells into one cell.
- `tableSplitCell` - Splits the selected cell into multiple cells.
- `tableToggleHeaderRow` - Toggles the header row of the table.
- `tableToggleHeaderCell` - Toggles the header cell of the table.
- `tableDelete` - Deletes the table.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `toolbarButtons()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Customizing floating toolbars

If your toolbar is too full, you can use a floating toolbar to show certain tools in a toolbar below the cursor, only when the user is inside a specific node type. This allows you to keep the main toolbar clean while still providing access to additional tools when needed.

You can customize the floating toolbars that appear when your cursor is placed inside a specific node by using the `floatingToolbars()` method.

In the example below, a floating toolbar appears when the cursor is inside a paragraph node. It shows bold, italic, and similar buttons. When the cursor is in a heading node, it displays heading-related buttons, and when inside a table, it shows table-specific controls.

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->floatingToolbars([
        'paragraph' => [
            'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript',
        ],
        'heading' => [
            'h1', 'h2', 'h3',
        ],
        'table' => [
            'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
            'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
            'tableMergeCells', 'tableSplitCell',
            'tableToggleHeaderRow', 'tableToggleHeaderCell',
            'tableDelete',
        ],
    ])
```

## Customizing text colors

The rich editor includes a text color tool for styling inline text. By default, it uses the [Tailwind CSS color palette](https://tailwindcss.com/docs/colors). In light mode, the 600 shades are applied to text, and in dark mode, the 400 shades are used.

You can customize which colors are available in the picker using the `textColors()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->textColors([
        '#ef4444' => 'Red',
        '#10b981' => 'Green',
        '#0ea5e9' => 'Sky',
    ])
```

If you would like to define different colors for light and dark mode, you can use the a `TextColor` object to define the color:

```php
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\TextColor;

RichEditor::make('content')
    ->textColors([
        'brand' => TextColor::make('Brand', '#0ea5e9'),
        'warning' => TextColor::make('Warning', '#f59e0b', darkColor: '#fbbf24'),
    ])
```

If you would like to add new colors onto the existing Tailwind palette, you can merge your colors into the `TextColor::getDefaults()` array:

```php
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\TextColor;

RichEditor::make('content')
    ->textColors([
        'brand' => TextColor::make('Brand', '#0ea5e9'),
        'warning' => TextColor::make('Warning', '#f59e0b', darkColor: '#fbbf24'),
        ...TextColor::getDefaults(),
    ])
```

When you use a `TextColor` object, the key of the array becomes the stored `data-color` attribute on the `<span>` tag, allowing you to reference the color in your CSS if needed. When you use the color as the array values, the actual color value (e.g., a HEX string) is stored as the `data-color` attribute.

You can also pass `textColors()` to the [content renderer](#rendering-rich-content) and [rich content attribute](#registering-rich-content-attributes) so that server-side rendering matches your editor configuration.

You can also allow users to pick custom colors that aren't in the predefined list by using the `customTextColors()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->textColors([
        // ...
    ])
    ->customTextColors()
```

You do not need to use `customTextColors()` on the [content renderer](#rendering-rich-content), as it will automatically render any custom colors that are used in the content.

## Rendering rich content

If you're [storing content as JSON](#storing-content-as-json) instead of HTML, or your content requires processing to inject [private image URLs](#using-private-images-in-the-editor) or similar, you'll need to use the `RichContentRenderer` tool in Filament to output HTML:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)->toHtml()
```

The `toHtml()` method returns a string. If you would like to output HTML in a Blade view without escaping the HTML, you can echo the `RichContentRender` object without calling `toHtml()`:

```blade
{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($record->content) }}
```

If you have configured the [file attachments behavior](#uploading-images-to-the-editor) of the editor to change the disk or visibility of the uploaded files, you must also pass these settings to the renderer to ensure that the correct URLs are generated:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsVisibility('private')
    ->toHtml()
```

If you are using [custom blocks](#using-custom-blocks) in the rich editor, you can pass an array of custom blocks to the renderer to ensure that they are rendered correctly:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->customBlocks([
        HeroBlock::class => [
            'categoryUrl' => $record->category->getUrl(),
        ],
        CallToActionBlock::class,
    ])
    ->toHtml()
```

If you are using [merge tags](#using-merge-tags), you can pass an array of values to replace the merge tags with:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->mergeTags([
        'name' => $record->user->name,
        'today' => now()->toFormattedDateString(),
    ])
    ->toHtml()
```

If you are using [custom text colors](#customizing-text-colors), you can pass an array of colors to the renderer to ensure that the colors are rendered correctly:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\TextColor;

RichContentRenderer::make($record->content)
    ->textColors([
        'brand' => TextColor::make('Brand', '#0ea5e9', darkColor: '#38bdf8'),
    ])
    ->toHtml();
```

### Styling the rendered content

The rich editor HTML uses a combination of HTML elements, CSS classes, and inline styles to style the content, depending on the features used in the editor. If you render the content in a Filament table column or infolist entry with `prose()`, Filament will automatically apply the necessary styles for you. If you are outputting the content in your own Blade view, you may need to add some additional styles to ensure that the content is styled correctly.

One way of styling the content is to use [Tailwind CSS Typography](https://tailwindcss.com/docs/typography-plugin). This plugin provides a set of pre-defined styles for common HTML elements, such as headings, paragraphs, lists, and tables. You can apply these styles to a container element using the `prose` class:

```blade
<div class="prose dark:prose-invert">
    {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($record->content) !!}
</div>
```

However, some features, such as the grid layout and text colors, require additional styles that are not included in the Tailwind CSS Typography plugin. Filament also includes its own `fi-prose` CSS class that adds these additional styles. Any app that loads Filament's `vendor/filament/support/resources/css/index.css` CSS will have access to this class. The styling is different to the `prose` class, but fits with Filament's design system better:

```blade
<div class="fi-prose">
    {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($record->content) !!}
</div>
```

## Security

By default, the editor outputs raw HTML, and sends it to the backend. Attackers are able to intercept the value of the component and send a different raw HTML string to the backend. As such, it is important that when outputting the HTML from a rich editor, it is sanitized; otherwise your site may be exposed to Cross-Site Scripting (XSS) vulnerabilities.

When Filament outputs raw HTML from the database in components such as `TextColumn` and `TextEntry`, it sanitizes it to remove any dangerous JavaScript. However, if you are outputting the HTML from a rich editor in your own Blade view, this is your responsibility. One option is to use Filament's `sanitizeHtml()` helper to do this, which is the same tool we use to sanitize HTML in the components mentioned above:

```blade
{!! str($record->content)->sanitizeHtml() !!}
```

If you're [storing content as JSON](#storing-content-as-json) instead of HTML, or your content requires processing to inject [private image URLs](#using-private-images-in-the-editor) or similar, you can use the [content renderer](#rendering-rich-content) to output HTML. This will automatically sanitize the HTML for you, so you don't need to worry about it.

## Uploading images to the editor

By default, uploaded images are stored publicly on your storage disk, so that the rich content stored in the database can be output easily anywhere. You may customize how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `fileAttachmentsDisk()`, `fileAttachmentsDirectory()`, and `fileAttachmentsVisibility()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<Aside variant="tip">
    Filament also supports [`spatie/laravel-medialibrary`](https://github.com/spatie/laravel-medialibrary) for storing rich editor file attachments. See our [plugin documentation](https://filamentphp.com/plugins/filament-spatie-media-library#using-media-library-for-rich-editor-file-attachments) for more information.
</Aside>

### Using private images in the editor

Using private images in the editor adds a layer of complexity to the process, since private images cannot be accessed directly via a permanent URL. Each time the editor is loaded or its content is rendered, temporary URLs need to be generated for each image, which are never stored in the database. Instead, Filament adds a `data-id` attribute to the image tags, which contains an identifier for the image in the storage disk, so that a temporary URL can be generated on demand.

When rendering the content using private images, ensure that you are using the [`RichContentRenderer` tool](#rendering-rich-content) in Filament to output HTML:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsVisibility('private')
    ->toHtml()
```

### Validating uploaded images

You may use the `fileAttachmentsAcceptedFileTypes()` method to control a list of accepted mime types for uploaded images. By default, `image/png`, `image/jpeg`, `image/gif`, and `image/webp` are accepted:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg'])
```

You may use the `fileAttachmentsMaxSize()` method to control the maximum file size for uploaded images. The size is specified in kilobytes. By default, the maximum size is 12288 KB (12 MB):

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsMaxSize(5120) // 5 MB
```

## Using custom blocks

Custom blocks are elements that users can drag and drop into the rich editor. You can define custom blocks that user can insert into the rich editor using the `customBlocks()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->customBlocks([
        HeroBlock::class,
        CallToActionBlock::class,
    ])
```

To create a custom block, you can use the following command:

```bash
php artisan make:filament-rich-content-custom-block HeroBlock
```

Each block needs a corresponding class that extends the `Filament\Forms\Components\RichEditor\RichContentCustomBlock` class. The `getId()` method should return a unique identifier for the block, and the `getLabel()` method should return the label that will be displayed in the editor's side panel:

```php
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class HeroBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getLabel(): string
    {
        return 'Hero section';
    }
}
```

When a user drags a custom block into the editor, you can choose to open a modal to collect additional information from the user before inserting the block. To do this, you can use the `configureEditorAction()` method to configure the [modal](../actions/modals) that will be opened when the block is inserted:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class HeroBlock extends RichContentCustomBlock
{
    // ...

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the hero section')
            ->schema([
                TextInput::make('heading')
                    ->required(),
                TextInput::make('subheading'),
            ]);
    }
}
```

The `schema()` method on the action can define form fields that will be displayed in the modal. When the user submits the form, the form data will be saved as "configuration" for that block.

### Rendering a preview for a custom block

Once a block is inserted into the editor, you may define a "preview" for it using the `toPreviewHtml()` method. This method should return a string of HTML that will be displayed in the editor when the block is inserted, allowing users to see what the block will look like before they save it. You can access the `$config` for the block in this method, which contains the data that was submitted in the modal when the block was inserted:

```php
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class HeroBlock extends RichContentCustomBlock
{
    // ...

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.hero.preview', [
            'heading' => $config['heading'],
            'subheading' => $config['subheading'] ?? 'Default subheading',
        ])->render();
    }
}
```

The `getPreviewLabel()` can be defined if you would like to customize the label that is displayed above the preview in the editor. By default, it will use the label defined in the `getLabel()` method, but the `getPreviewLabel()` is able to access the `$config` for the block, allowing you to display dynamic information in the label:

```php
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class HeroBlock extends RichContentCustomBlock
{
    // ...

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        return "Hero section: {$config['heading']}";
    }
}
```

### Rendering content with custom blocks

When rendering the rich content, you can pass the array of custom blocks to the `RichContentRenderer` to ensure that the blocks are rendered correctly:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->customBlocks([
        HeroBlock::class,
        CallToActionBlock::class,
    ])
    ->toHtml()
```

Each block class may have a `toHtml()` method that returns the HTML that should be rendered for that block:

```php
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class HeroBlock extends RichContentCustomBlock
{
    // ...

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.hero.index', [
            'heading' => $config['heading'],
            'subheading' => $config['subheading'],
            'buttonLabel' => 'View category',
            'buttonUrl' => $data['categoryUrl'],
        ])->render();
    }
}
```

As seen above, the `toHtml()` method receives two parameters: `$config`, which contains the configuration data submitted in the modal when the block was inserted, and `$data`, which contains any additional data that may be needed to render the block. This allows you to access the configuration data and render the block accordingly. The data can be passed in the `customBlocks()` method:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->customBlocks([
        HeroBlock::class => [
            'categoryUrl' => $record->category->getUrl(),
        ],
        CallToActionBlock::class,
    ])
    ->toHtml()
```

### Opening the custom blocks panel by default

If you want the custom blocks panel to be open by default when the rich editor is loaded, you can use the `activePanel('customBlocks')` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->customBlocks([
        HeroBlock::class,
        CallToActionBlock::class,
    ])
    ->activePanel('customBlocks')
```

## Using merge tags

Merge tags allow the user to insert "placeholders" into their rich content, which can be replaced with dynamic values when the content is rendered. This is useful for inserting things like the current user's name, or the current date.

To register merge tags on an editor, use the `mergeTags()` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->mergeTags([
        'name',
        'today',
    ])
```

Merge tags are surrounded by double curly braces, like `{{ name }}`. When the content is rendered, these tags will be replaced with the corresponding values.

To insert a merge tag into the content, users can start typing `{{` to search for a tag to insert. Alternatively, they can click on the "merge tags" tool in the editor's toolbar, which opens a panel containing all the merge tags. They can then drag a merge tag from the editor's side panel into the content or click to insert it.

### Rendering content with merge tags

When rendering the rich content, you can pass an array of values to replace the merge tags with:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->mergeTags([
        'name' => $record->user->name,
        'today' => now()->toFormattedDateString(),
    ])
    ->toHtml()
```

If you have many merge tags or you need to run some logic to determine the values, you can use a function as the value of each merge tag. This function will be called when a merge tag is first encountered in the content, and its result is cached for subsequent tags of the same name:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($record->content)
    ->mergeTags([
        'name' => fn (): string => $record->user->name,
        'today' => now()->toFormattedDateString(),
    ])
    ->toHtml()
```

#### Using HTML content in merge tags

By default, merge tags render their values as plain text. However, you can render HTML content in merge tags by providing values that implement Laravel's `Htmlable` interface. This is useful for inserting formatted content, links, or other HTML elements:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\HtmlString;

RichContentRenderer::make($record->content)
    ->mergeTags([
        'user_name' => $record->user->name, // Plain text
        'user_profile_link' => new HtmlString('<a href="' . route('profile', $record->user) . '">View Profile</a>'),
    ])
    ->toHtml()
```

When a merge tag value implements the `Htmlable` interface (such as `HtmlString`), the system automatically detects this and renders the HTML content without escaping it. Non-`Htmlable` values continue to be rendered as plain text for security.

### Using custom merge tag labels

You may provide custom labels for merge tags that will be displayed in the editor's side panel and content preview using an associative array where the keys are the merge tag names and the values are the labels:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->mergeTags([
        'name' => 'Full name',
        'today' => 'Today\'s date',
    ])
```

The labels aren't saved in the content of the editor and are only used for display purposes.

### Opening the merge tags panel by default

If you want the merge tags panel to be open by default when the rich editor is loaded, you can use the `activePanel('mergeTags')` method:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->mergeTags([
        'name',
        'today',
    ])
    ->activePanel('mergeTags')
```

## Registering rich content attributes

There are elements of the rich editor configuration that apply to both the editor and the renderer. For example, if you are using [private images](#using-private-images-in-the-editor), [custom blocks](#using-custom-blocks), [merge tags](#using-merge-tags), or [plugins](#extending-the-rich-editor), you need to ensure that the same configuration is used in both places. To do this, Filament provides you with a way to register rich content attributes that can be used in both the editor and the renderer.

To register rich content attributes on an Eloquent model, you should use the `InteractsWithRichContent` trait and implement the `HasRichContent` interface. This allows you to register the attributes in the `setUpRichContent()` method:

```php
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements HasRichContent
{
    use InteractsWithRichContent;

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->fileAttachmentsDisk('s3')
            ->fileAttachmentsVisibility('private')
            ->customBlocks([
                HeroBlock::class => [
                    'categoryUrl' => fn (): string => $this->category->getUrl(),
                ],
                CallToActionBlock::class,
            ])
            ->mergeTags([
                'name' => fn (): string => $this->user->name,
                'today' => now()->toFormattedDateString(),
            ])
            ->mergeTagLabels([
                'name' => 'Full name',
                'today' => 'Today\'s date',
            ])
            ->textColors(
                'brand' => TextColor::make('Brand', '#0ea5e9', darkColor: '#38bdf8'),
            )
            ->customTextColors()
            ->plugins([
                HighlightRichContentPlugin::make(),
            ]);
    }
}
```

Whenever you use the `RichEditor` component, the configuration registered for the corresponding attribute will be used:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
```

To easily render the rich content HTML from a model with the given configuration, you can call the `renderRichContent()` method on the model, passing the name of the attribute:

```blade
{!! $record->renderRichContent('content') !!}
```

Alternatively, you can get an `Htmlable` object to render without escaping the HTML:

```blade
{{ $record->getRichContentAttribute('content') }}
```

When using a [text column](../tables/columns/text) in a table or a [text entry](../infolists/text-entry) in an infolist, you don't need to manually render the rich content. Filament will do this for you automatically:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('content')

TextEntry::make('content')
```

## Extending the rich editor

You can create plugins for the rich editor, which allow you to add custom TipTap extensions to the editor and renderer, as well as custom toolbar buttons. Create a new class that implements the `RichContentPlugin` interface:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;
use Tiptap\Marks\Highlight;

class HighlightRichContentPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        // This method should return an array of PHP TipTap extension objects.
        // See: https://github.com/ueberdosis/tiptap-php
    
        return [
            app(Highlight::class, [
                'options' => ['multicolor' => true],
            ]),
        ];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        // This method should return an array of URLs to JavaScript files containing
        // TipTap extensions that should be asynchronously loaded into the editor
        // when the plugin is used.
    
        return [
            FilamentAsset::getScriptSrc('rich-content-plugins/highlight'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        // This method should return an array of `RichEditorTool` objects, which can then be
        // used in the `toolbarButtons()` of the editor.
        
        // The `jsHandler()` method allows you to access the TipTap editor instance
        // through `$getEditor()`, and `chain()` any TipTap commands to it.
        // See: https://tiptap.dev/docs/editor/api/commands
        
        // The `action()` method allows you to run an action (registered in the `getEditorActions()`
        // method) when the toolbar button is clicked. This allows you to open a modal to
        // collect additional information from the user before running a command.
    
        return [
            RichEditorTool::make('highlight')
                ->jsHandler('$getEditor()?.chain().focus().toggleHighlight().run()')
                ->icon(Heroicon::CursorArrowRays),
            RichEditorTool::make('highlightWithCustomColor')
                ->action(arguments: '{ color: $getEditor().getAttributes(\'highlight\')?.[\'data-color\'] }')
                ->icon(Heroicon::CursorArrowRipple),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        // This method should return an array of `Action` objects, which can be used by the tools
        // registered in the `getEditorTools()` method. The name of the action should match
        // the name of the tool that uses it.
        
        // The `runCommands()` method allows you to run TipTap commands on the editor instance.
        // It accepts an array of `EditorCommand` objects that define the command to run,
        // as well as any arguments to pass to the command. You should also pass in the
        // `editorSelection` argument, which is the current selection in the editor
        // to apply the commands to.
    
        return [
            Action::make('highlightWithCustomColor')
                ->modalWidth(Width::Large)
                ->fillForm(fn (array $arguments): array => [
                    'color' => $arguments['color'] ?? null,
                ])
                ->schema([
                    ColorPicker::make('color'),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'toggleHighlight',
                                arguments: [[
                                    'color' => $data['color'],
                                ]],
                            ),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }
}
```

You can use the `plugins()` method to register your plugin with the rich editor and [rich content renderer](#rendering-rich-content):

```php
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichEditor::make('content')
    ->toolbarButtons([
        ['bold', 'highlight', 'highlightWithCustomColor'],
        ['h2', 'h3'],
        ['bulletList', 'orderedList'],
    ])
    ->plugins([
        HighlightRichContentPlugin::make(),
    ])

RichContentRenderer::make($record->content)
    ->plugins([
        HighlightRichContentPlugin::make(),
    ])
```

### Setting up a TipTap JavaScript extension

Filament is able to asynchronously load JavaScript extensions for TipTap. To do this, you need to create a JavaScript file that contains the extension, and register it in the `getTipTapJsExtensions()` method of your [plugin](#extending-the-rich-editor).

For instance, if you wanted to use the [TipTap highlight extension](https://tiptap.dev/docs/editor/extensions/marks/highlight), make sure it is installed first:

```bash
npm install @tiptap/extension-highlight --save-dev
```

Then, create a JavaScript file that imports the extension. In this example, create a file called `highlight.js` in the `resources/js/filament/rich-content-plugins` directory, and add the following code to it:

```javascript
import Highlight from '@tiptap/extension-highlight'

export default Highlight.configure({
    multicolor: true,
})
```

One way to compile this file is to use [esbuild](https://esbuild.github.io). You can install it using `npm`:

```bash
npm install esbuild --save-dev
```

You must create an [esbuild](https://esbuild.github.io) script to compile the file. You can put this anywhere, for example `bin/build.js`:

```js
import * as esbuild from 'esbuild'

async function compile(options) {
    const context = await esbuild.context(options)

    await context.rebuild()
    await context.dispose()
}

compile({
    define: {
        'process.env.NODE_ENV': `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: false,
    sourcesContent: false,
    treeShaking: true,
    target: ['es2020'],
    minify: true,
    entryPoints: ['./resources/js/filament/rich-content-plugins/highlight.js'],
    outfile: './resources/js/dist/filament/rich-content-plugins/highlight.js',
})
```

As you can see at the bottom of the script, we are compiling a file called `resources/js/filament/rich-content-plugins/highlight.js` into `resources/js/dist/filament/rich-content-plugins/highlight.js`. You can change these paths to suit your needs. You can compile as many files as you want.

To run the script and compile this file into `resources/js/dist/filament/rich-content-plugins/highlight.js` run the following command:

```bash
node bin/build.js
```

You should register it in the `boot()` method of a service provider, like `AppServiceProvider`, and use `loadedOnRequest()` so that it is not downloaded until the rich editor is loaded on a page:

```php
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

FilamentAsset::register([
    Js::make('rich-content-plugins/highlight', __DIR__ . '/../../resources/js/dist/filament/rich-content-plugins/highlight.js')->loadedOnRequest(),
]);
```

To publish this new JavaScript file into the `/public` directory of your app so that it can be served, you can use the `filament:assets` command:

```bash
php artisan filament:assets
```

In the [plugin object](#extending-the-rich-editor), the `getTipTapJsExtensions()` method should return the path to the JavaScript file you just created. Now that it's registered with `FilamentAsset`, you can use the `getScriptSrc()` method to get the URL to the file:

```php
use Filament\Support\Facades\FilamentAsset;

/**
 * @return array<string>
 */
public function getTipTapJsExtensions(): array
{
    return [
        FilamentAsset::getScriptSrc('rich-content-plugins/highlight'),
    ];
}
```
