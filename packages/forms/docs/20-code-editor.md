---
title: Code editor
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The code editor component allows you to write code in a textarea with line numbers. By default, no syntax highlighting is applied.

```php
use Filament\Forms\Components\CodeEditor;

CodeEditor::make('code')
```

<AutoScreenshot name="forms/fields/code-editor/simple" alt="Code editor" version="4.x" />

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
- XML
- YAML

You can open the `Filament\Forms\Components\CodeEditor\Enums\Language` enum class to see this list. To switch to using JavaScript syntax highlighting, you can use the `Language::JavaScript` enum value:

```php
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;

CodeEditor::make('code')
    ->language(Language::JavaScript)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `language()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/code-editor/language" alt="Code editor with syntax highlighting" version="4.x" />
