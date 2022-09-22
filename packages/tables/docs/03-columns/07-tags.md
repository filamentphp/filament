---
title: Tags column
---

Tags columns render a list of tags from an array:

```php
use Filament\Tables\Columns\TagsColumn;

TagsColumn::make('tags')
```

Be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];

    // ...
}
```

## Using a separator

Instead of using an array, you may use a separated string by passing the separator into `separator()`:

```php
use Filament\Tables\Columns\TagsColumn;

TagsColumn::make('tags')->separator(',')
```
