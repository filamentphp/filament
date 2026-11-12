---
title: Editing records
---

<AutoScreenshot name="panels/resources/editing" alt="Resource edit page" />

## Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may define a `mutateFormDataBeforeFill()` method on the Edit page class to modify the `$data` array, and return the modified version before it is filled into the form:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../actions/edit#customizing-data-before-filling-the-form).

## Refreshing form data

If an action modifies a record while you are editing it, you can refresh selected data in the form using the `refreshFormData()` method:

```php
use App\Models\Post;
use Filament\Actions\Action;

Action::make('approve')
    ->action(function (Post $record) {
        $record->approve();

        $this->refreshFormData([
            'status',
        ]);
    })
```

Pass the form state paths you want to refresh. Other fields retain their unsaved edits unless your customization code changes them. Refreshing a parent path or relationship field can replace unsaved changes within it, so prefer the narrowest paths you need rather than refilling the entire form.

The refreshed data uses `mutateFormDataBeforeFill()` and the fields' hydration callbacks, but not the page's `beforeFill()` or `afterFill()` hooks.

`refreshFormData()` uses the page's current record, not a fresh database query. If the record was updated elsewhere, call `$this->getRecord()->refresh()` first. Relationship fields may otherwise reuse relationships already loaded on the record.

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may define a `mutateFormDataBeforeSave()` method on the Edit page class, which accepts the `$data` as an array, and returns it modified:

```php
protected function mutateFormDataBeforeSave(array $data): array
{
    $data['last_edited_by_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../actions/edit#customizing-data-before-saving).

## Resolving conflicting edits

You can opt in to concurrent-edit protection by adding the `ResolvesFormConflicts` trait to your Edit page:

```php
use Filament\Resources\Pages\Concerns\ResolvesFormConflicts;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use ResolvesFormConflicts;

    // ...
}
```

Without this trait or a configured handler, existing save behavior is unchanged. Protected pages compare the original form values, the user's draft, and the record's current values before saving. Changes to different fields are combined automatically: the user's edits are retained, and current saved values replace fields the user has not edited. If both writers chose the same value, no conflict is raised. This does not require websockets, model event subscriptions, a version column, or changes to external writers. [Edit actions](../actions/edit#resolving-conflicting-edits) can opt in independently.

When both writers change a field differently, Filament stops before validation/save hooks, upload storage, relationship saves, or saved notifications. An action modal compares the draft and current values for each conflict. The user can choose their value or the saved value independently for each field, then save normally. Dismissing the modal retains the draft. Choosing a value does not save anything. A choice is accepted only if the displayed values are still current; otherwise, the comparison refreshes automatically. Saving checks for newer conflicting changes again. The user can also discard the whole draft and load current values.

The draft and saved previews reuse disabled instances of your configured form fields, with separate state that is never saved. Field actions and update callbacks are disabled. Custom fields must respect their disabled state. Pending uploads that Livewire cannot preview display their file name and size instead of a media preview.

The boundary is a whole field, including repeaters, builders, multi-selects, and auxiliary state such as uploaded file names. Changes to different items of the same repeater therefore require a whole-field choice rather than automatically combining rows. Separate fields using paths such as `settings.title` and `settings.summary` remain separate boundaries, even when persisted in one JSON attribute. Incoming values pass through the normal validation and save transformations before they are persisted.

Reordering a reorderable `KeyValue` counts as editing that field, including when it is inside a repeater or builder. Your database storage must preserve object key ordering if you need to persist that order; native MySQL JSON and PostgreSQL JSONB objects do not. Pending uploads are compared using an opaque temporary identity and their original name, without storing the file. Replacing an upload after displaying a conflict requires reviewing the new value before choosing it.

Filament rechecks resource scope and edit authorization before reading current values. The comparison and save run in a transaction on the record's database connection, holding its write lock until the save finishes. This transaction is required even when optional panel database transactions are disabled.

> [!WARNING]
> This protects fields saved to the resource record by `save()` and `saveFormComponentOnly()`, not arbitrary actions or independently written related records, pivots, or other database connections. A section save checks only its fields and halts its enclosing action on conflict, so code after `saveFormComponentOnly()` does not report success. Custom save implementations must keep their record writes on the locked connection. It is not a transaction around external services or a universal conflict/merge engine.

Comparison uses loaded form values and field state casts, before save-time dehydration transformations. Reading current values runs `mutateFormDataBeforeFill()` and field hydration callbacks, but not the page's `beforeFill()` / `afterFill()` hooks. Keep these data-loading callbacks free of writes or external side effects. Values generated only by page fill hooks may need to be excluded from comparison.

Only current differences count: a value changed and then changed back does not cause a conflict. Changes to attributes outside persisted form fields do not reject a save. The base and draft are retained while the form remains open, not across reloads or session expiry. Explicitly refreshing form paths with `refreshFormData()` updates their baseline without accepting changes to the rest of the form.

### Updating untouched values live

When your panel and model are configured for [live record updates](../realtime/model-events#refreshing-resource-pages), protected Edit pages also update fields the user has not edited. Each accepted field advances its comparison baseline. Fields changed locally retain their values and original baseline, so saving still detects conflicting edits. Your `statePathGroups()` boundaries apply to live updates too: editing one field in a group keeps the whole group unchanged.

Background updates do not validate or save the form, run save hooks, or open the conflict modal. Filament reads current values through the configured form mapping and current resource authorization. Changes typed while a refresh is in flight are retained; an obsolete background response is discarded before its form values or baseline are accepted, then the comparison is fetched again. A refresh batched with a user action leaves that action's result intact and reconciles afterward.

While a conflict comparison is open, automatic field updates pause so its displayed choices remain stable. Choosing a value and saving still recheck current values. Ignoring a path for save-time conflict detection does not permit live updates to replace local changes to it. There is no autosave or persistence of unsaved work across reloads.

### Ignoring regenerated values

Some form values are derived from other fields and deliberately regenerated on every save. For example, a hidden `search_text` field might contain searchable text built from the title and body. A background job rebuilding `search_text` should not cause a conflict with a regenerated draft value: the next save will calculate it again from the combined source fields.

Configure the handler by overriding `getFormEditConflictHandler()` on your Edit page:

```php
use Filament\Forms\EditConflictHandler;

protected function getFormEditConflictHandler(): ?EditConflictHandler
{
    return EditConflictHandler::make()
        ->ignoredStatePaths([
            'search_text',
            'totals.calculated_tax',
        ]);
}
```

The shared `EditConflictHandler` owns comparison policy, recovery choices, and schema-based rendering. The page and action integrations supply authorized record reads and the save transaction. You can pass the same handler configuration to an Edit action; configuring a page does not automatically enable its actions.

Paths are relative to the form's data, without the `data.` prefix. You can exclude a whole nested value, such as `totals`, or use `*` to match item paths, such as `items.*.calculated_total`. Excluding a parent also excludes its descendants.

An exclusion only changes comparison. It does not remove the value from the form or comparison snapshots, stop it being saved, recompute it, or change validation and authorization. Keep your existing save-time calculation in place so it uses the combined source fields, not an earlier draft. The source fields, such as the title and body, should remain protected.

> [!CAUTION]
> Do not exclude editable values simply to make conflicts disappear. An ignored value can be overwritten without a stale warning. Exclude it only when replacing the stored value on every save is intentional.

### Customizing conflict previews

The conflict modal uses schema components and actions. You can extend `EditConflictHandler` and return your implementation from `getFormEditConflictHandler()`, without replacing authorization or atomic save logic:

- Override `getSchemaComponents()` to add context around the conflict list or replace its overall layout.
- Override `getContentComponent(string $statePath)` to change the layout of one conflict group.
- Override `getLabel(string $statePath)` to label a group that does not correspond to one field.
- Override `getPreviewComponent(string $statePath, string $source)` to add context to a preview or replace it with other schema components. `$source` is either `'draft'` or `'current'`.
- Override `getChoiceAction(string $statePath, string $source)` to configure a choice action, such as its label or icon.
- Override `getAction()` to configure the modal itself, such as its heading, width, or footer actions.

For example, you can wrap each configured preview with additional context:

```php
use Filament\Forms\EditConflictHandler;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;

class PostEditConflictHandler extends EditConflictHandler
{
    protected function getPreviewComponent(string $statePath, string $source): Component
    {
        return Group::make([
            Text::make($source === 'draft' ? 'Your proposed values' : 'Currently saved values'),
            parent::getPreviewComponent($statePath, $source),
        ]);
    }
}
```

Return `PostEditConflictHandler::make()` from the page's `getFormEditConflictHandler()` method, or pass it to an action's `editConflictHandler()` method.

`$statePath` identifies a comparison group, not necessarily a model attribute or field. Default previews render the configured fields represented by that group's member paths. Forms that map several inputs into one attribute, or use nested input names unrelated to model columns, can keep their existing `mutateFormDataBeforeFill()` and `mutateFormDataBeforeSave()` mappings. Configure which inputs must be chosen together using `statePathGroups()` on the handler. Override `snapshot(Schema $form)` or `compare()` in your handler for specialized comparison representations; the supplied snapshot form is isolated from the editable form. Keep these methods read-only.

The default previews keep their own form state and component ownership, separate from the modal schema and editable draft. Wrapping the preview component preserves this isolation. If you replace it, prefer non-editable schema components and the handler's retained `getOutcome()[$source]` values rather than binding new inputs to the draft or querying a newer value that does not match the displayed choice. Authorize any additional context you load yourself. Rendering overrides do not extend protection to other records or change the comparison boundaries.

### Reusing comparison data

The page retains `baseFormState` and, after a conflicting save, `staleFormState`. These are server-controlled Livewire properties. The structured outcome contains `base`, `draft`, `current`, `changedPaths`, `conflictingPaths`, `incomingPaths`, `statePathGroups`, `orderedStatePaths`, and `ignoredStatePaths`; it does not reduce the comparison to a hash. `draft` is the snapshot of the attempted save, while the form remains editable.

The reusable `Filament\Schemas\StateComparison` compares these state arrays without reading records, authorizing access, or saving anything:

```php
use Filament\Schemas\StateComparison;

$comparison = StateComparison::make()
    ->base($base)
    ->draft($draft)
    ->current($current)
    ->statePathGroups([
        'title' => ['title'],
        'tags' => ['tags'],
    ])
    ->ignoredStatePaths(['search_text']);

$hasConflicts = $comparison->hasConflicts();
$conflictingPaths = $comparison->getConflictingPaths();
$incomingPaths = $comparison->getIncomingPaths();
$outcome = $comparison->toArray();
```

`statePathGroups` defines the comparison boundaries independently of the three-way classification. Each group has an identifier and one or more state paths that must be chosen together. Without explicit groups, each top-level key is a boundary. Resource Edit pages supply groups from whole fields, including their auxiliary output. This separation allows later boundary policies to reuse comparison and recovery; it does not provide item identity or safe row-level merging today.

Keep every member of a custom group within the same section when using `saveFormComponentOnly()`. A section save that would split a group throws a `LogicException` before saving rather than silently removing its protection.

Associative key ordering is ignored unless you configure `orderedStatePaths()`. These paths also support `*`. Resource Edit pages configure them for reorderable `KeyValue` fields automatically. List item ordering remains significant.

`conflictingPaths` and `incomingPaths` contain group identifiers. `changedPaths` remains the list of differences between `base` and `current`, not a list of conflicts. Ignored values remain available in all three snapshots. Authorize and filter any current values before passing them to a client; using `StateComparison` alone provides no write protection or permission checks.

## Customizing the saving process

You can tweak how the record is updated using the `handleRecordUpdate()` method on the Edit page class:

```php
use Illuminate\Database\Eloquent\Model;

protected function handleRecordUpdate(Model $record, array $data): Model
{
    $record->update($data);

    return $record;
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../actions/edit#customizing-the-saving-process).

## Customizing redirects

By default, saving the form will not redirect the user to another page.

You may set up a custom redirect when the form is saved by overriding the `getRedirectUrl()` method on the Edit page class.

For example, the form can redirect back to the [List page](listing-records) of the resource:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

Or the [View page](viewing-records):

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
}
```

If you wish to be redirected to the previous page, else the index page:

```php
protected function getRedirectUrl(): string
{
    return $this->previousUrl ?? $this->getResource()::getUrl('index');
}
```

You can also use the [configuration](../panel-configuration) to customize the default redirect page for all resources at once:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->resourceEditPageRedirect('index') // or
        ->resourceEditPageRedirect('view');
}
```

## Customizing the save notification

When the record is successfully updated, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, define a `getSavedNotificationTitle()` method on the edit page class:

```php
protected function getSavedNotificationTitle(): ?string
{
    return 'User updated';
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../actions/edit#customizing-the-save-notification).

You may customize the entire notification by overriding the `getSavedNotification()` method on the edit page class:

```php
use Filament\Notifications\Notification;

protected function getSavedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('User updated')
        ->body('The user has been saved successfully.');
}
```

To disable the notification altogether, return `null` from the `getSavedNotification()` method on the edit page class:

```php
use Filament\Notifications\Notification;

protected function getSavedNotification(): ?Notification
{
    return null;
}
```

## Lifecycle hooks

Hooks may be used to execute code at various points within a page's lifecycle, like before a form is saved. To set up a hook, create a protected method on the Edit page class with the name of the hook:

```php
protected function beforeSave(): void
{
    // ...
}
```

In this example, the code in the `beforeSave()` method will be called before the data in the form is saved to the database.

There are several available hooks for the Edit pages:

```php
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function beforeFill(): void
    {
        // Runs before the form fields are populated from the database.
    }

    protected function afterFill(): void
    {
        // Runs after the form fields are populated from the database.
    }

    protected function beforeValidate(): void
    {
        // Runs before the form fields are validated when the form is saved.
    }

    protected function afterValidate(): void
    {
        // Runs after the form fields are validated when the form is saved.
    }

    protected function beforeSave(): void
    {
        // Runs before the form fields are saved to the database.
    }

    protected function afterSave(): void
    {
        // Runs after the form fields are saved to the database.
    }
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../actions/edit#lifecycle-hooks).

### Defining lifecycle hooks in traits

To define a lifecycle hook in a trait, suffix the hook name with the trait's name. This follows the `boot{TraitName}()` convention used by Eloquent and the `mount{TraitName}()` convention used by Livewire, allowing reusable traits to hook into the page lifecycle without colliding with hooks defined on the page itself:

```php
use Filament\Resources\Pages\EditRecord;

trait HandlesDrafts
{
    protected function afterSaveHandlesDrafts(): void
    {
        // Runs after the form fields are saved to the database, in addition
        // to the hook on the page.
    }
}

class EditUser extends EditRecord
{
    use HandlesDrafts;

    protected function afterSave(): void
    {
        // Both lifecycle hooks are called.
    }
}
```

The page's own hook is called first, followed by each trait hook. Hooks from traits used by other traits are also called. Trait hooks are called automatically, so you should not also call them from the page's own hook.

## Saving a part of the form independently

You may want to allow the user to save a part of the form independently of the rest of the form. One way to do this is with a [section action in the header or footer](../schemas/sections#inserting-actions-and-other-components-in-the-footer-of-a-section). From the `action()` method, you can call `saveFormComponentOnly()`, passing in the `Section` component that you want to save:

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;

Section::make('Rate limiting')
    ->schema([
        // ...
    ])
    ->footerActions([
        fn (string $operation): Action => Action::make('save')
            ->action(function (Section $component, EditRecord $livewire) {
                $livewire->saveFormComponentOnly($component);
                
                Notification::make()
                    ->title('Rate limiting saved')
                    ->body('The rate limiting settings have been saved successfully.')
                    ->success()
                    ->send();
            })
            ->visible($operation === 'edit'),
    ])
```

The `$operation` helper is available, to ensure that the action is only visible when the form is being edited.

<AutoScreenshot name="panels/resources/editing-section-actions" alt="Resource edit page with section footer save action" />

## Halting the saving process

At any time, you may call `$this->halt()` from inside a lifecycle hook or mutation method, which will halt the entire saving process:

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;

protected function beforeSave(): void
{
    if (! $this->getRecord()->team->subscribed()) {
        Notification::make()
            ->warning()
            ->title('You don\'t have an active subscription!')
            ->body('Choose a plan to continue.')
            ->persistent()
            ->actions([
                Action::make('subscribe')
                    ->button()
                    ->url(route('subscribe'), shouldOpenInNewTab: true),
            ])
            ->send();

        $this->halt();
    }
}
```

Alternatively, if you're editing records in a modal action, check out the [Actions documentation](../actions/edit#halting-the-saving-process).

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the Edit page if the `update()` method of the model policy returns `true`.

They also have the ability to delete the record if the `delete()` method of the policy returns `true`.

## Custom actions

"Actions" are buttons that are displayed on pages, which allow the user to run a Livewire method on the page or visit a URL.

On resource pages, actions are usually in 2 places: in the top right of the page, and below the form.

For example, you may add a new button action next to "Delete" on the Edit page:

```php
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getHeaderActions(): array
    {
        return [
            Action::make('impersonate')
                ->action(function (): void {
                    // ...
                }),
            DeleteAction::make(),
        ];
    }
}
```

<AutoScreenshot name="panels/resources/editing-header-actions" alt="Resource edit page with custom header actions" />

Or, a new button next to "Save" below the form:

```php
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            Action::make('close')->action('saveAndClose'),
        ];
    }

    public function saveAndClose(): void
    {
        // ...
    }
}
```

To view the entire actions API, please visit the [pages section](../navigation/custom-pages#adding-actions-to-pages).

### Adding a save action button to the header

The "Save" button can be added to the header of the page by overriding the `getHeaderActions()` method and using `getSaveFormAction()`. You need to pass `formId()` to the action, to specify that the action should submit the form with the ID of `form`, which is the `<form>` ID used in the view of the page:

```php
protected function getHeaderActions(): array
{
    return [
        $this->getSaveFormAction()
            ->formId('form'),
    ];
}
```

You may remove all actions from the form by overriding the `getFormActions()` method to return an empty array:

```php
protected function getFormActions(): array
{
    return [];
}
```

<AutoScreenshot name="panels/resources/editing-save-in-header" alt="Resource edit page with save action in the header" />

## Creating another Edit page

One Edit page may not be enough space to allow users to navigate many form fields. You can create as many Edit pages for a resource as you want. This is especially useful if you are using [resource sub-navigation](overview#resource-sub-navigation), as you are then easily able to switch between the different Edit pages.

To create an Edit page, you should use the `make:filament-page` command:

```bash
php artisan make:filament-page EditCustomerContact --resource=CustomerResource --type=EditRecord
```

You must register this new page in your resource's `getPages()` method:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListCustomers::route('/'),
        'create' => Pages\CreateCustomer::route('/create'),
        'view' => Pages\ViewCustomer::route('/{record}'),
        'edit' => Pages\EditCustomer::route('/{record}/edit'),
        'edit-contact' => Pages\EditCustomerContact::route('/{record}/edit/contact'),
    ];
}
```

Now, you can define the `form()` for this page, which can contain other fields that are not present on the main Edit page:

```php
use Filament\Schemas\Schema;

public function form(Schema $schema): Schema
{
    return $schema
        ->components([
            // ...
        ]);
}
```

## Adding edit pages to resource sub-navigation

If you're using [resource sub-navigation](overview#resource-sub-navigation), you can register this page as normal in `getRecordSubNavigation()` of the resource:

```php
use App\Filament\Resources\Customers\Pages\EditCustomerContact;
use Filament\Resources\Pages\Page;

public static function getRecordSubNavigation(Page $page): array
{
    return $page->generateNavigationItems([
        // ...
        EditCustomerContact::class,
    ]);
}
```

## Custom page content

Each page in Filament has its own [schema](../schemas), which defines the overall structure and content. You can override the schema for the page by defining a `content()` method on it. The `content()` method for the Edit page contains the following components by default:

```php
use Filament\Schemas\Schema;

public function content(Schema $schema): Schema
{
    return $schema
        ->components([
            $this->getFormContentComponent(), // This method returns a component to display the form that is defined in this resource
            $this->getRelationManagersContentComponent(), // This method returns a component to display the relation managers that are defined in this resource
        ]);
}
```

Inside the `components()` array, you can insert any [schema component](../schemas). You can reorder the components by changing the order of the array or remove any of the components that are not needed.

### Using a custom Blade view

For further customization opportunities, you can override the `$view` property on the page class to a custom view in your app:

```php
protected string $view = 'filament.resources.users.pages.edit-user';
```

This assumes that you have created a view at `resources/views/filament/resources/users/pages/edit-user.blade.php`:

```blade
<x-filament-panels::page>
    {{-- `$this->getRecord()` will return the current Eloquent record for this page --}}
    
    {{ $this->content }} {{-- This will render the content of the page defined in the `content()` method, which can be removed if you want to start from scratch --}}
</x-filament-panels::page>
```
