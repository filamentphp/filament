---
title: Code editor
---

## Introduction

The code editor component allows you to write code in a textarea with line numbers. By default, no syntax highlighting is applied.

```php
use Filament\Forms\Components\CodeEditor;

CodeEditor::make('code')
```

<AutoScreenshot name="forms/fields/code-editor/simple" alt="Code editor" />

## Using language syntax highlighting

You may change the language syntax highlighting of the code editor using the `language()` method. The editor supports the following languages:

- C++
- CSS
- Go
- HTML
- Java
- JavaScript
- JSON
- Markdown
- PHP
- Python
- SQL
- XML
- YAML

You can open the `Filament\Forms\Components\CodeEditor\Enums\Language` enum class to see this list. To switch to using JavaScript syntax highlighting, you can use the `Language::JavaScript` enum value:

```php
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;

CodeEditor::make('code')
    ->language(Language::JavaScript)
```

<UtilityInjection set="formFields">As well as allowing a static value, the `language()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/code-editor/language" alt="Code editor with syntax highlighting" />

### Loading custom languages

To use a language that is not built in, pass the URL of a JavaScript module to `language()`. Filament loads the module when the editor is initialized.

For example, to highlight Nginx configuration, install its parser and a bundler in your application:

```bash
npm install --save-dev @codemirror/legacy-modes esbuild
```

Create `resources/js/code-editor-nginx.js`. The module must export a default function that returns a CodeMirror extension or array of extensions:

```javascript
import { nginx } from '@codemirror/legacy-modes/mode/nginx'

export default ({ language: { StreamLanguage } }) => StreamLanguage.define(nginx)
```

Compile it as an ES module into your application's public directory:

```bash
npx esbuild resources/js/code-editor-nginx.js --bundle --format=esm --outfile=public/js/code-editor-nginx.js
```

You can now use the module's URL in your field:

```php
use Filament\Forms\Components\CodeEditor;

CodeEditor::make('configuration')
    ->language(asset('js/code-editor-nginx.js'))
```

The function receives the `language`, `state`, and `view` modules from Filament's bundled CodeMirror packages. Use these to construct extensions instead of bundling separate copies of CodeMirror, which can cause compatibility errors.

## Allowing lines to wrap

By default, long lines in the code editor will create a horizontal scrollbar. If you would like to allow long lines to wrap instead, you may use the `wrap()` method:

```php
use Filament\Forms\Components\CodeEditor;

CodeEditor::make('code')
    ->wrap()
```

<AutoScreenshot name="forms/fields/code-editor/wrap" alt="Code editor with line wrapping" />
