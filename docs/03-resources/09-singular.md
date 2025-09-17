---
title: Singular resources
---

## Overview

Resources aren't the only way to interact with Eloquent records in a Filament panel. Even though resources may solve many of your requirements, the "index" (root) page of a resource contains a table with a list of records in that resource.

Sometimes there is no need for a table that lists records in a resource. There is only a single record that the user interacts with. If it doesn't yet exist when the user visits the page, it gets created when the form is first submitted by the user to save it. If the record already exists, it is loaded into the form when the page is first loaded, and updated when the form is submitted.

For example, a CMS might have a `Page` Eloquent model and a `PageResource`, but you may also want to create a singular page outside the `PageResource` for editing the "homepage" of the website. This allows the user to directly edit the homepage without having to navigate to the `PageResource` and find the homepage record in the table.

Other examples of this include a "Settings" page, or a "Profile" page for the currently logged-in user. For these use cases, though, we recommend that you use the [Spatie Settings plugin](/plugins/filament-spatie-settings) and the [Profile](../users#authentication-features) features of Filament, which require less code to implement.

## Creating a singular resource

Although there is no specific "singular resource" feature in Filament, it is a highly-requested behavior and can be implemented quite simply using a [custom page](../navigation/custom-pages) with a [form](../forms). This guide will explain how to do this.

Firstly, create a [custom page](../navigation/custom-pages):

```bash
php artisan make:filament-page ManageHomepage
```

This command will create two files - a page class in the `/Filament/Pages` directory of your resource directory, and a Blade view in the `/filament/pages` directory of the resource views directory.

The page class should contain the following elements:
- A `$data` property, which will hold the current state of the form.
- A `mount()` method, which will load the current record from the database and fill the form with its data. If the record doesn't exist, `null` will be passed to the `fill()` method of the form, which will assign any default values to the form fields.
- A `form()` method, which will define the form schema. The form contains fields in the `components()` method. The `record()` method should be used to specify the record that the form should load relationship data from. The `statePath()` method should be used to specify the name of the property (`$data`) where the form's state should be stored.
- A `save()` method, which will save the form data to the database. The `getState()` method runs form validation and returns valid form data. This method should check if the record already exists, and if not, create a new one. The `wasRecentlyCreated` property of the model can be used to determine if the record was just created, and if so then any relationships should be saved as well. A notification is sent to the user to confirm that the record was saved.
- A `getRecord()` method, while not strictly necessary, is a good idea to have. This method will return the Eloquent record that the form is editing. It can be used across the other methods to avoid code duplication.

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
<x-filament::page>
    {{ $this->form }}
</x-filament::page>
```
