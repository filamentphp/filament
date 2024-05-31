---
title: Make it your own (using custom stubs)
---

# Publishing stubs

If you would like to customize the stubs that are published by Filament, you can do so by publishing them to your application. This will allow you to modify the stubs to fit your needs.
Simply run the following command:

```bash
php artisan vendor:publish --tag=filament-stubs
```

This will publish the stubs to the `stubs\filament` directory in your application's root. From there, you can modify the stubs to fit your needs and whenever you run any `make` command, Filament will use your custom stubs.

The following files will be published:

- Cluster.stub
- Column.stub
- ColumnView.stub
- CreateForm.stub
- CustomResourcePage.stub
- EditForm.stub
- Exporter.stub
- Field.stub
- FieldView.stub
- Form.stub
- FormView.stub
- Importer.stub
- LayoutComponent.stub
- LayoutComponentView.stub
- Page.stub
- PageView.stub
- RelationManager.stub
- Resource.stub
- ResourceEditPage.stub
- ResourceListPage.stub
- ResourceManagePage.stub
- ResourceManageRelatedRecordsPage.stub
- ResourcePage.stub
- ResourceViewPage.stub
- SettingsPage.stub
- Table.stub
- TableView.stub
- ThemeCss.stub
- ThemePostcssConfig.stub
- ThemeTailwindConfig.stub