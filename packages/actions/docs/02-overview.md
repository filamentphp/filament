---
title: Overview
---

## What is an action?

"Action" is a word that is used quite a bit within the Laravel community. Traditionally, action PHP classes handle "doing" something in your application's business logic. For instance, logging a user in, sending an email, or creating a new user record in the database.

In Filament, actions also handle "doing" something in your app. However, they are a bit different from traditional actions. They are designed to be used in the context of a user interface. For instance, you might have a button to delete a client record, which opens a modal to confirm your decision. When the user clicks the "Delete" button in the modal, the client is deleted. This whole workflow is an "action".

```php
Action::make('delete')
    ->requiresConfirmation()
    ->action(fn () => $this->client->delete())
```

Actions can also collect extra information from the user. For instance, you might have a button to email a client. When the user clicks the button, a modal opens to collect the email subject and body. When the user clicks the "Send" button in the modal, the email is sent:

```php
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Mail;

Action::make('sendEmail')
    ->form([
        TextInput::make('subject')->required(),
        RichEditor::make('body')->required(),
    ])
    ->action(function (array $data) {
        Mail::to($this->client)
            ->send(new GenericEmail(
                subject: $data['subject'],
                body: $data['body'],
            ));
    })
```

Usually, actions get executed without redirecting the user away from the page. This is because we extensively use Livewire. However, actions can be much simpler, and don't even need a modal. You can pass a URL to an action, and when the user clicks on the button, they are redirected to that page:

```php
Action::make('edit')
    ->url(fn (): string => route('posts.edit', ['post' => $this->post]))
```

The entire look of the action's trigger button and the modal is customizable using fluent PHP methods. We provide a sensible and consistent styling for the UI, but all of this is customizable with CSS.

## Types of action

The concept of "actions" is used throughout Filament in many contexts. Some contexts don't support opening modals from actions - they can only open a URL, call a public Livewire method, or dispatch a Livewire event. Additionally, different contexts use different action PHP classes since they provide the developer context-aware data that is appropriate to that use-case.

### Custom Livewire component actions

You can add an action to any Livewire component in your app, or even a page in a [panel](../panels/pages).

These actions use the `Filament\Actions\Action` class. They can open a modal if you choose, or even just a URL.

If you're looking to add an action to a Livewire component, [visit this page](adding-an-action-to-a-livewire-component) in the docs. If you want to add an action to the header of a page in a panel, [visit this page](../panels/pages#header-actions) instead.

### Table actions

Filament's tables also use actions. Actions can be added to the end of any table row, or even in the header of a table. For instance, you may want an action to "create" a new record in the header, and then "edit" and "delete" actions on each row. Additionally, actions can be added to any table column, such that each cell in that column is a trigger for your action.

These actions use the `Filament\Tables\Actions\Action` class. They can open a modal if you choose, or even just a URL.

If you're looking to add an action to a table in your app, [visit this page](../tables/actions) in the docs.

#### Table bulk actions

Tables also support "bulk actions". These can be used when the user selects rows in the table. Traditionally, when rows are selected, a "bulk actions" button appears in the top left corner of the table. When the user clicks this button, they are presented with a dropdown menu of actions to choose from. Bulk actions may also be added to the header of a table, next to other header actions. In this case, bulk action trigger buttons are disabled until the user selects table rows.

These actions use the `Filament\Tables\Actions\BulkAction` class. They can open modals if you choose.

If you're looking to add a bulk action to a table in your app, [visit this page](../tables/actions#bulk-actions) in the docs.

### Form component actions

Form components can contain actions. A good use case for actions inside form components would be with a select field, and an action button to "create" a new record. When you click on the button, a modal opens to collect the new record's data. When the modal form is submitted, the new record is created in the database, and the select field is filled with the newly created record. Fortunately, [this case is handled for you out of the box](../forms/fields/select#creating-new-records), but it's a good example of how form component actions can be powerful.

These actions use the `Filament\Forms\Components\Actions\Action` class. They can open a modal if you choose, or even just a URL.

If you're looking to add an action to a form component in your app, [visit this page](../forms/actions) in the docs.

### Infolist component actions

Infolist components can contain actions. These use the `Filament\Infolists\Components\Actions\Action` class. They can open a modal if you choose, or even just a URL.

If you're looking to add an action to an infolist component in your app, [visit this page](../infolists/actions) in the docs.

### Notification actions

When you [send notifications](../notifications/sending-notifications), you can add actions. These buttons are rendered below the content of the notification. For example, a notification to alert the user that they have a new message should contain an action button that opens the conversation thread.

These actions use the `Filament\Notifications\Actions\Action` class. They aren't able to open modals, but they can open a URL or dispatch a Livewire event.

If you're looking to add an action to a notification in your app, [visit this page](../notifications/sending-notifications#adding-actions-to-notifications) in the docs.

### Global search result actions

In the Panel Builder, there is a [global search](../panels/resources/global-search) field that allows you to search all resources in your app from one place. When you click on a search result, it leads you to the resource page for that record. However, you may add additional actions below each global search result. For example, you may want both "Edit" and "View" options for a client search result, so the user can quickly edit their profile as well as view it in read-only mode.

These actions use the `Filament\GlobalSearch\Actions\Action` class. They aren't able to open modals, but they can open a URL or dispatch a Livewire event.

If you're looking to add an action to a global search result in a panel, [visit this page](../panels/resources/global-search#adding-actions-to-global-search-results) in the docs.

## Prebuilt actions

Filament includes several prebuilt actions that you can add to your app. Their aim is to simplify the most common Eloquent-related actions:

- [Create](prebuilt-actions/create)
- [Edit](prebuilt-actions/edit)
- [View](prebuilt-actions/view)
- [Delete](prebuilt-actions/delete)
- [Replicate](prebuilt-actions/replicate)
- [Force-delete](prebuilt-actions/force-delete)
- [Restore](prebuilt-actions/restore)
- [Import](prebuilt-actions/import)
- [Export](prebuilt-actions/export)

## Grouping actions

You may group actions together into a dropdown menu by using an `ActionGroup` object. Groups may contain many actions, or other groups:

```php
ActionGroup::make([
    Action::make('view'),
    Action::make('edit'),
    Action::make('delete'),
])
```

To learn about how to group actions, see the [Grouping actions](grouping-actions) page.
