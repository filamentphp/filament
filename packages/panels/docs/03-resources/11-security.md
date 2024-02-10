---
title: Security
---

## Mass assignment

Filament always passes validated form data to the model, and nothing else. As a result, when saving data we disable mass assignment protection, meaning that data will be saved regardless of your `$fillable` or `$guarded` configuration. This allows other parts of your application to guard attributes without having to worry about panels.
