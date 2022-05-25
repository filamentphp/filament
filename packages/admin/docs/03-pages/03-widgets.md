---
title: Widgets
---

Filament allows you to display [widgets](../dashboard) inside pages, below the header and above the footer.

To register a widget on a page, use the `getHeaderWidgets()` or `getFooterWidgets()` methods:

```php
use App/Filament/Widgets/StatsOverviewWidget;

protected function getHeaderWidgets(): array
{
    return [
        StatsOverviewWidget::class
    ];
}
```
