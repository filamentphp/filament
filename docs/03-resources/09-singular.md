---
title: Singular resources
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Resources aren't the only way to interact with Eloquent records in a Filament panel. Even though resources may solve many of your requirements, the "index" (root) page of a resource contains a table with a list of records in that resource.

Sometimes there is no need for a table that lists records in a resource. There is only a single record that the user interacts with. If it doesn't yet exist when the user visits the page, it gets created when the form is first submitted by the user to save it. If the record already exists, it is loaded into the form when the page is first loaded, and updated when the form is submitted.

For example, a CMS might have a `Page` Eloquent model and a `PageResource`, but you may also want to create a singular page outside the `PageResource` for editing the "homepage" of the website. This allows the user to directly edit the homepage without having to navigate to the `PageResource` and find the homepage record in the table.

Other examples of this include a "Settings" page, or a "Profile" page for the currently logged-in user. For these use cases, though, we recommend that you use the [Spatie Settings plugin](https://filamentphp.com/plugins/filament-spatie-settings) and the [Profile](../users/overview#authentication-features) features of Filament, which require less code to implement.

<AutoScreenshot name="panels/resources/singular" alt="A singular resource page for managing a homepage" version="6.x" />

## Creating a singular resource

Use a [custom page](../navigation/custom-pages) with a [form](../forms) to manage a single record without a resource table.

Firstly, create a [custom page](../navigation/custom-pages):

```bash
php artisan make:filament-page ManageHomepage
```

This creates a page class and a Blade view in your panel's configured page directories.

The example below loads the homepage or uses form defaults if it does not exist.

<Aside variant="warning">
    Custom pages do not automatically apply resource policies. Authorize access to the page and the create or update operation, and apply any tenant or ownership restrictions your application requires to the query.
</Aside>

```php
namespace App\Filament\Pages;

use App\Models\WebsitePage;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

/**
 * @property-read Schema $form
 */
class ManageHomepage extends Page
{
    protected string $view = 'filament.pages.manage-homepage';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('content'),
                    // ...
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $record = $this->getRecord();
        
        if (! $record) {
            $record = new WebsitePage();
            $record->is_homepage = true;
        }
        
        $record->fill($data);
        $record->save();
        
        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('Saved')
            ->send();
    }
    
    public function getRecord(): ?WebsitePage
    {
        return WebsitePage::query()
            ->where('is_homepage', true)
            ->first();
    }
}
```

The page Blade view should render the form:

```blade
<x-filament-panels::page>
    {{ $this->form }}
</x-filament-panels::page>
```
