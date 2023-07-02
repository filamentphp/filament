---
title: Checkbox column
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The checkbox column allows you to render a checkbox inside the table, which can be used to update that database record without needing to open a new page or a modal:

```php
use Filament\Tables\Columns\CheckboxColumn;

CheckboxColumn::make('is_admin')
```

<AutoScreenshot name="tables/columns/checkbox/simple" alt="Checkbox column" version="3.x" />
