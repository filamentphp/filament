---
title: Toggle column
---

The toggle column allows you to render a toggle button inside the table, which can be used to update that database record without needing to open a new page or a modal:

```php
use Filament\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_admin')
```
