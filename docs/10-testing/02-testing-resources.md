---
title: Testing resources
---

## Authenticating as a user

Ensure that you are authenticated to access the app in your `TestCase`:

```php
use App\Models\User;

protected function setUp(): void
{
    parent::setUp();

    $this->actingAs(User::factory()->create());
}
```

Alternatively, if you are using Pest you can use a `beforeEach()` function at the top of your test file to authenticate:

```php
use App\Models\User;

beforeEach(function () {
    $user = User::factory()->create();

    actingAs($user);
});
```

## Testing a resource list page

To test if the list page is able to load, test the list page as a Livewire component, and call `assertOk()` to ensure that the HTTP response was 200 OK. You can also use the `assertCanSeeTableRecords()` method to check if records are being displayed in the table:

```php
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;

it('can load the page', function () {
    $users = User::factory()->count(5)->create();

    livewire(ListUsers::class)
        ->assertOk()
        ->assertCanSeeTableRecords($users);
});
```

To test the table on the list page, you should visit the [Testing tables](testing-tables) section. To test any actions in the header of the page or actions in the table, you should visit the [Testing actions](testing-actions) section. Below are some common examples of other tests that you can run on the list page.

To [test that the table search is working](testing-tables#testing-that-a-column-can-be-searched), you can use the `searchTable()` method to search for a specific record. You can also use the `assertCanSeeTableRecords()` and `assertCanNotSeeTableRecords()` methods to check if the correct records are being displayed in the table:

```php
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;

it('can search users by `name` or `email`', function () {
    $users = User::factory()->count(5)->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
});
```

To [test that the table sorting is working](testing-tables#testing-that-a-column-can-be-sorted), you can use the `sortTable()` method to sort the table by a specific column. You can also use the `assertCanSeeTableRecords()` method to check if the records are being displayed in the correct order:

```php
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;

it('can sort users by `name`', function () {
    $users = User::factory()->count(5)->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->sortTable('name')
        ->assertCanSeeTableRecords($users->sortBy('name'), inOrder: true)
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords($users->sortByDesc('name'), inOrder: true);
});
```

To [test that the table filtering is working](testing-tables#testing-filters), you can use the `filterTable()` method to filter the table by a specific column. You can also use the `assertCanSeeTableRecords()` and `assertCanNotSeeTableRecords()` methods to check if the correct records are being displayed in the table:

```php
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;

it('can filter users by `locale`', function () {
    $users = User::factory()->count(5)->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->filterTable('locale', $users->first()->locale)
        ->assertCanSeeTableRecords($users->where('locale', $users->first()->locale))
        ->assertCanNotSeeTableRecords($users->where('locale', '!=', $users->first()->locale));
});
```

To [test that the table bulk actions are working](testing-actions#testing-table-bulk-actions), you can use the `selectTableRecords()` method to select multiple records in the table. You can also use the `callAction()` method to call a specific action on the selected records:

```php
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use function Pest\Laravel\assertDatabaseMissing;

it('can bulk delete users', function () {
    $users = User::factory()->count(5)->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->selectTableRecords($users)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertNotified()
        ->assertCanNotSeeTableRecords($users);

    $users->each(fn (User $user) => assertDatabaseMissing($user));
});
```

## Testing a resource create page

To test if the create page is able to load, test the create page as a Livewire component, and call `assertOk()` to ensure that the HTTP response was 200 OK:

```php
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;

it('can load the page', function () {
    livewire(CreateUser::class)
        ->assertOk();
});
```

To test the form on the create page, you should visit the [Testing schemas](testing-schemas) section. To test any actions in the header of the page or in the form, you should visit the [Testing actions](testing-actions) section. Below are some common examples of other tests that you can run on the create page.

To test that the form is creating records correctly, you can use the `fillForm()` method to fill in the form fields, and then use the `call('create')` method to create the record. You can also use the `assertNotified()` method to check if a notification was displayed, and the `assertRedirect()` method to check if the user was redirected to another page:

```php
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;

it('can create a user', function () {
    $newUserData = User::factory()->make();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newUserData->name,
            'email' => $newUserData->email,
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => $newUserData->name,
        'email' => $newUserData->email,
    ]);
});
```

To test that the form is validating properly, you can use the `fillForm()` method to fill in the form fields, and then use the `call('create')` method to create the record. You can also use the `assertHasFormErrors()` method to check if the form has any errors, and the `assertNotNotified()` method to check if no notification was displayed. You can also use the `assertNoRedirect()` method to check if the user was not redirected to another page. In this example, we use a [Pest dataset](https://pestphp.com/docs/datasets#content-bound-datasets) to test multiple rules without having to repeat the test code:

```php
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use Illuminate\Support\Str;

it('validates the form data', function (array $data, array $errors) {
    $newUserData = User::factory()->make();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newUserData->name,
            'email' => $newUserData->email,
            ...$data,
        ])
        ->call('create')
        ->assertHasFormErrors($errors)
        ->assertNotNotified()
        ->assertNoRedirect();
})->with([
    '`name` is required' => [['name' => null], ['name' => 'required']],
    '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
    '`email` is a valid email address' => [['email' => Str::random()], ['email' => 'email']],
    '`email` is required' => [['email' => null], ['email' => 'required']],
    '`email` is max 255 characters' => [['email' => Str::random(256)], ['email' => 'max']],
]);
```

## Testing a resource edit page

To test if the edit page is able to load, test the edit page as a Livewire component, and call `assertOk()` to ensure that the HTTP response was 200 OK. You can also use the `assertSchemaStateSet()` method to check if the form fields are set to the correct values:

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;

it('can load the page', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});
```

To test the form on the edit page, you should visit the [Testing schemas](testing-schemas) section. To test any actions in the header of the page or in the form, you should visit the [Testing actions](testing-actions) section. Below are some common examples of other tests that you can run on the edit page.

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;

it('can update a user', function () {
    $user = User::factory()->create();

    $newUserData = User::factory()->make();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->fillForm([
            'name' => $newUserData->name,
            'email' => $newUserData->email,
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(User::class, [
        'id' => $user->id,
        'name' => $newUserData->name,
        'email' => $newUserData->email,
    ]);
});
```

To test that the form is validating properly, you can use the `fillForm()` method to fill in the form fields, and then use the `call('save')` method to save the record. You can also use the `assertHasFormErrors()` method to check if the form has any errors, and the `assertNotNotified()` method to check if no notification was displayed. In this example, we use a [Pest dataset](https://pestphp.com/docs/datasets#content-bound-datasets) to test multiple rules without having to repeat the test code:

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Illuminate\Support\Str;

it('validates the form data', function (array $data, array $errors) {
    $user = User::factory()->create();

    $newUserData = User::factory()->make();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->fillForm([
            'name' => $newUserData->name,
            'email' => $newUserData->email,
            ...$data,
        ])
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();
})->with([
    '`name` is required' => [['name' => null], ['name' => 'required']],
    '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
    '`email` is a valid email address' => [['email' => Str::random()], ['email' => 'email']],
    '`email` is required' => [['email' => null], ['email' => 'required']],
    '`email` is max 255 characters' => [['email' => Str::random(256)], ['email' => 'max']],
]);
```

To [test that an action is working](testing-actions), such as the `DeleteAction`, you can use the `callAction()` method to call the delete action. You can also use the `assertNotified()` method to check if a notification was displayed, and the `assertRedirect()` method to check if the user was redirected to another page:

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Filament\Actions\DeleteAction;
use function Pest\Laravel\assertDatabaseMissing;

it('can delete a user', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseMissing($user);
});
```

## Testing a resource view page

To test if the view page is able to load, test the view page as a Livewire component, and call `assertOk()` to ensure that the HTTP response was 200 OK. You can also use the `assertSchemaStateSet()` method to check if the infolist entries are set to the correct values:

```php
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;

it('can load the page', function () {
    $user = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $user->id,
    ])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});
```

To test the infolist on the view page, you should visit the [Testing schemas](testing-schemas) section. To test any actions in the header of the page or in the infolist, you should visit the [Testing actions](testing-actions) section.

## Testing relation managers

To test if a relation manager is rendered on a page, such as the edit page of a resource, you can use the `assertSeeLivewire()` method to check if the relation manager is being rendered:

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\PostsRelationManager;
use App\Models\User;

it('can load the relation manager', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->assertSeeLivewire(PostsRelationManager::class);
});
```

Since relation managers are Livewire components, you can also test a relation manager's functionality itself, like its ability to load successfully with a 200 OK response, with the correct records in the table. When testing a relation manager, you need to pass in the `ownerRecord`, which is the record from the resource you are inside, and the `pageClass`, which is the class of the page you are on:

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\PostsRelationManager;
use App\Models\Post;
use App\Models\User;

it('can load the relation manager', function () {
    $user = User::factory()
        ->has(Post::factory()->count(5))
        ->create();

    livewire(PostsRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords($user->posts);
});
```

You can [test searching](testing-tables#testing-that-a-column-can-be-searched), [sorting](testing-tables#testing-that-a-column-can-be-sorted), and [filtering](testing-tables#testing-filters) in the same way as you would on a resource list page.

You can also [test actions](testing-actions), for example, the `CreateAction` in the header of the table:

```php
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\PostsRelationManager;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use function Pest\Laravel\assertDatabaseHas;

it('can create a post', function () {
    $user = User::factory()->create();

    $newPostData = Post::factory()->make();

    livewire(PostsRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'title' => $newPostData->title,
            'content' => $newPostData->content,
        ])
        ->assertNotified();

    assertDatabaseHas(Post::class, [
        'title' => $newPostData->title,
        'content' => $newPostData->content,
        'user_id' => $user->id,
    ]);
});
```

## Testing create / edit page `getFormActions()`

When testing actions in `getFormActions()` on a resource page, use the `schemaComponent()` method targeting the `form-actions` key. For example, if you have a custom `Action::make('createAndVerifyEmail')` action in the `getFormActions()` method of your `CreateUser` page, you can test it like this:

```php
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

it('can create a user and verify their email address', function () {
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])
        ->callAction(TestAction::make('createAndVerifyEmail')->schemaComponent('form-actions'));

    expect(User::query()->where('email', 'test@example.com')->first())
        ->hasVerifiedEmail()->toBeTrue();
});
```

## Testing multiple panels

If you have multiple panels and you would like to test a non-default panel, you will need to tell Filament which panel you are testing. This can be done in the `setUp()` method of the test case, or you can do it at the start of a particular test. Filament usually does this in a middleware when you access the panel through a request, so if you're not making a request in your test like when testing a Livewire component, you need to set the current panel manually:

```php
use Filament\Facades\Filament;

Filament::setCurrentPanel('app'); // Where `app` is the ID of the panel you want to test.
```
