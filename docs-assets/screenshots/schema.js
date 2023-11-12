export default {
    'actions/trigger-button/button': {
        url: 'actions',
        selector: '#buttonAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/link': {
        url: 'actions',
        selector: '#linkAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/icon-button': {
        url: 'actions',
        selector: '#iconButtonAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#iconButtonAction button')
        },
    },
    'actions/trigger-button/badge': {
        url: 'actions',
        selector: '#badgeAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/danger': {
        url: 'actions',
        selector: '#dangerAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/large': {
        url: 'actions',
        selector: '#largeAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/icon': {
        url: 'actions',
        selector: '#iconAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/icon-after': {
        url: 'actions',
        selector: '#iconAfterAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/trigger-button/badged': {
        url: 'actions',
        selector: '#badgedAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#badgedAction button')
        },
    },
    'actions/trigger-button/success-badged': {
        url: 'actions',
        selector: '#successBadgedAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#successBadgedAction button')
        },
    },
    'actions/trigger-button/outlined': {
        url: 'actions',
        selector: '#outlinedAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'actions/modal/confirmation': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#confirmationModalAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: 'body',
    },
    'actions/modal/confirmation-custom-text': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#confirmationModalCustomTextAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: 'body',
    },
    'actions/modal/icon': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#modalIconAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: 'body',
    },
    'actions/modal/form': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#modalFormAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: 'body',
    },
    'actions/modal/wizard': {
        url: 'actions',
        viewport: {
            width: 1080,
            height: 480,
            deviceScaleFactor: 2,
        },
        before: async (page) => {
            await page.click('#wizardAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: 'body',
    },
    'actions/modal/slide-over': {
        url: 'actions',
        viewport: {
            width: 1920,
            height: 720,
            deviceScaleFactor: 2,
        },
        before: async (page) => {
            await page.click('#slideOverAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        selector: 'body',
    },
    'actions/group/simple': {
        url: 'actions',
        selector: '#actionGroup',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#actionGroup button')
            await page.waitForSelector('#actionGroup .fi-dropdown-list')

            await page.hover('#actionGroup .fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/group/customized': {
        url: 'actions',
        selector: '#customizedActionGroup',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#customizedActionGroup button')
            await page.waitForSelector('#customizedActionGroup .fi-dropdown-list')

            await page.hover('#customizedActionGroup .fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/group/placement': {
        url: 'actions',
        selector: '#actionGroupPlacement',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#actionGroupPlacement button')
            await page.waitForSelector('#actionGroupPlacement .fi-dropdown-list')

            await page.hover('#actionGroupPlacement .fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/group/nested': {
        url: 'actions',
        selector: '#nestedActionGroups',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#nestedActionGroups button')
            await page.waitForSelector('#nestedActionGroups .fi-dropdown-list')

            await page.hover('#nestedActionGroups .fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'app/dashboard': {
        url: 'admin',
        selector: 'body',
    },
    'forms/getting-started/fields': {
        url: 'forms/getting-started',
        selector: '#fields',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/getting-started/columns': {
        url: 'forms/getting-started',
        selector: '#columns',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/getting-started/column-span': {
        url: 'forms/getting-started',
        selector: '#columnSpan',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/getting-started/section': {
        url: 'forms/getting-started',
        selector: '#section',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/simple': {
        url: 'forms/fields',
        selector: '#simple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/helper-text': {
        url: 'forms/fields',
        selector: '#helperText',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/hint': {
        url: 'forms/fields',
        selector: '#hint',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/hint-color': {
        url: 'forms/fields',
        selector: '#hintColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/hint-icon': {
        url: 'forms/fields',
        selector: '#hintIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/disabled': {
        url: 'forms/fields',
        selector: '#disabled',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/placeholder': {
        url: 'forms/fields',
        selector: '#placeholder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/simple': {
        url: 'forms/fields',
        selector: '#textInput',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/affix': {
        url: 'forms/fields',
        selector: '#textInputAffix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/suffix-icon': {
        url: 'forms/fields',
        selector: '#textInputSuffixIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/simple': {
        url: 'forms/fields',
        selector: '#select',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/javascript': {
        url: 'forms/fields',
        selector: '#javascriptSelect',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#javascriptSelect .choices')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/searchable': {
        url: 'forms/fields',
        selector: '#searchableSelect',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#searchableSelect .choices')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/multiple': {
        url: 'forms/fields',
        selector: '#multipleSelect',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#multipleSelect .choices')
            await page.click('#multipleSelect .choices #choices--datamultipleSelect-item-choice-2')
            await page.click('#multipleSelect .choices #choices--datamultipleSelect-item-choice-3')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/grouped': {
        url: 'forms/fields',
        selector: '#groupedSelect',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#groupedSelect .choices')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/create-option': {
        url: 'forms/fields',
        selector: '#createSelectOption',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/create-option-modal': {
        url: 'forms/fields',
        selector: 'body',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#createSelectOption button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/edit-option': {
        url: 'forms/fields',
        selector: '#editSelectOption',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/edit-option-modal': {
        url: 'forms/fields',
        selector: 'body',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#editSelectOption button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/affix': {
        url: 'forms/fields',
        selector: '#selectAffix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/suffix-icon': {
        url: 'forms/fields',
        selector: '#selectSuffixIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox/simple': {
        url: 'forms/fields',
        selector: '#checkbox',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox/inline': {
        url: 'forms/fields',
        selector: '#inlineCheckbox',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox/not-inline': {
        url: 'forms/fields',
        selector: '#notInlineCheckbox',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/simple': {
        url: 'forms/fields',
        selector: '#toggle',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/icons': {
        url: 'forms/fields',
        selector: '#toggleIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/off-color': {
        url: 'forms/fields',
        selector: '#toggleOffColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/on-color': {
        url: 'forms/fields',
        selector: '#toggleOnColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/inline': {
        url: 'forms/fields',
        selector: '#inlineToggle',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/not-inline': {
        url: 'forms/fields',
        selector: '#notInlineToggle',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/simple': {
        url: 'forms/fields',
        selector: '#checkboxList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/option-descriptions': {
        url: 'forms/fields',
        selector: '#checkboxListOptionDescriptions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/columns': {
        url: 'forms/fields',
        selector: '#checkboxListColumns',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/rows': {
        url: 'forms/fields',
        selector: '#checkboxListRows',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/searchable': {
        url: 'forms/fields',
        selector: '#searchableCheckboxList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/bulk-toggleable': {
        url: 'forms/fields',
        selector: '#bulkToggleableCheckboxList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/simple': {
        url: 'forms/fields',
        selector: '#radio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/option-descriptions': {
        url: 'forms/fields',
        selector: '#radioOptionDescriptions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/boolean': {
        url: 'forms/fields',
        selector: '#booleanRadio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/inline': {
        url: 'forms/fields',
        selector: '#inlineRadio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/disabled-option': {
        url: 'forms/fields',
        selector: '#disabledOptionRadio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/simple': {
        url: 'forms/fields',
        selector: '#dateTimePickers',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/without-seconds': {
        url: 'forms/fields',
        selector: '#dateTimePickerWithoutSeconds',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/javascript': {
        url: 'forms/fields',
        selector: '#javascriptDateTimePicker',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#javascriptDateTimePicker button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/date-time-picker/display-format': {
        url: 'forms/fields',
        selector: '#dateTimePickerDisplayFormat',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/week-starts-on-sunday': {
        url: 'forms/fields',
        selector: '#dateTimePickerWeekStartsOnSunday',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#dateTimePickerWeekStartsOnSunday button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/date-time-picker/disabled-dates': {
        url: 'forms/fields',
        selector: '#dateTimePickerDisabledDates',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#dateTimePickerDisabledDates button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/date-time-picker/affix': {
        url: 'forms/fields',
        selector: '#dateTimePickerAffix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/prefix-icon': {
        url: 'forms/fields',
        selector: '#dateTimePickerPrefixIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/file-upload/simple': {
        url: 'forms/fields',
        selector: '#fileUpload',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/rich-editor/simple': {
        url: 'forms/fields',
        selector: '#richEditor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/markdown-editor/simple': {
        url: 'forms/fields',
        selector: '#markdownEditor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Ensure that the Markdown editor is visible otherwise its JS won't load.
            await page.evaluate(() => {
                document.querySelector('#markdownEditor').scrollIntoView()
            })

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/repeater/simple': {
        url: 'forms/fields',
        selector: '#repeater',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/repeater/reorderable-with-buttons': {
        url: 'forms/fields',
        selector: '#repeaterReorderableWithButtons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/repeater/collapsed': {
        url: 'forms/fields',
        selector: '#collapsedRepeater',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/repeater/cloneable': {
        url: 'forms/fields',
        selector: '#cloneableRepeater',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/repeater/grid': {
        url: 'forms/fields',
        selector: '#gridRepeater',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/repeater/labelled': {
        url: 'forms/fields',
        selector: '#labelledRepeater',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/repeater/simple-one-field': {
        url: 'forms/fields',
        selector: '#simpleRepeater',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/simple': {
        url: 'forms/fields',
        selector: '#builder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/labelled': {
        url: 'forms/fields',
        selector: '#labelledBuilder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/icons': {
        url: 'forms/fields',
        selector: '#builderIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#builderIcons .fi-fo-builder-block-picker button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/builder/reorderable-with-buttons': {
        url: 'forms/fields',
        selector: '#builderReorderableWithButtons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/collapsed': {
        url: 'forms/fields',
        selector: '#collapsedBuilder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/cloneable': {
        url: 'forms/fields',
        selector: '#cloneableBuilder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/tags-input/simple': {
        url: 'forms/fields',
        selector: '#tagsInput',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/textarea/simple': {
        url: 'forms/fields',
        selector: '#textarea',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/key-value/simple': {
        url: 'forms/fields',
        selector: '#keyValue',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/key-value/reorderable': {
        url: 'forms/fields',
        selector: '#reorderableKeyValue',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/color-picker/simple': {
        url: 'forms/fields',
        selector: '#colorPicker',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/actions/suffix': {
        url: 'forms/fields',
        selector: '#suffixAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/actions/hint': {
        url: 'forms/fields',
        selector: '#hintAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/fieldset/simple': {
        url: 'forms/layout',
        selector: '#fieldset',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/tabs/simple': {
        url: 'forms/layout',
        selector: '#tabs',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/tabs/icons': {
        url: 'forms/layout',
        selector: '#tabsIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/tabs/icons-after': {
        url: 'forms/layout',
        selector: '#tabsIconsAfter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/tabs/badges': {
        url: 'forms/layout',
        selector: '#tabsBadges',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/wizard/simple': {
        url: 'forms/layout',
        selector: '#wizard',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/wizard/icons': {
        url: 'forms/layout',
        selector: '#wizardIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/wizard/descriptions': {
        url: 'forms/layout',
        selector: '#wizardDescriptions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/section/simple': {
        url: 'forms/layout',
        selector: '#section',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/section/icons': {
        url: 'forms/layout',
        selector: '#sectionIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/section/aside': {
        url: 'forms/layout',
        selector: '#sectionAside',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/section/collapsed': {
        url: 'forms/layout',
        selector: '#sectionCollapsed',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/section/compact': {
        url: 'forms/layout',
        selector: '#sectionCompact',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/section/without-header': {
        url: 'forms/layout',
        selector: '#sectionWithoutHeader',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/placeholder/simple': {
        url: 'forms/layout',
        selector: '#placeholder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/actions/anonymous/simple': {
        url: 'forms/layout',
        selector: '#anonymousActions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/actions/anonymous/full-width': {
        url: 'forms/layout',
        selector: '#anonymousActionsFullWidth',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/actions/anonymous/horizontally-aligned-center': {
        url: 'forms/layout',
        selector: '#anonymousActionsHorizontallyAlignedCenter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/actions/anonymous/vertically-aligned-end': {
        url: 'forms/layout',
        selector: '#anonymousActionsVerticallyAlignedEnd',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/simple': {
        url: 'infolists/entries',
        selector: '#simple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/placeholder': {
        url: 'infolists/entries',
        selector: '#placeholder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/helper-text': {
        url: 'infolists/entries',
        selector: '#helperText',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/hint': {
        url: 'infolists/entries',
        selector: '#hint',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/hint-color': {
        url: 'infolists/entries',
        selector: '#hintColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/hint-icon': {
        url: 'infolists/entries',
        selector: '#hintIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/tooltips': {
        url: 'infolists/entries',
        selector: '#tooltips',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#tooltips .fi-in-text')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'infolists/entries/text/simple': {
        url: 'infolists/entries',
        selector: '#text',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/badge': {
        url: 'infolists/entries',
        selector: '#textBadge',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/list': {
        url: 'infolists/entries',
        selector: '#textList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/bullet-list': {
        url: 'infolists/entries',
        selector: '#textBulletList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/color': {
        url: 'infolists/entries',
        selector: '#textColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/icon': {
        url: 'infolists/entries',
        selector: '#textIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/icon-after': {
        url: 'infolists/entries',
        selector: '#textIconAfter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/large': {
        url: 'infolists/entries',
        selector: '#textLarge',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/bold': {
        url: 'infolists/entries',
        selector: '#textBold',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/mono': {
        url: 'infolists/entries',
        selector: '#textMono',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/copyable': {
        url: 'infolists/entries',
        selector: '#textCopyable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#textCopyable .fi-in-text-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        }
    },
    'infolists/entries/icon/simple': {
        url: 'infolists/entries',
        selector: '#icon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/icon/color': {
        url: 'infolists/entries',
        selector: '#iconColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/icon/medium': {
        url: 'infolists/entries',
        selector: '#iconMedium',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/icon/boolean': {
        url: 'infolists/entries',
        selector: '#iconBoolean',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/icon/boolean-icon': {
        url: 'infolists/entries',
        selector: '#iconBooleanIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/icon/boolean-color': {
        url: 'infolists/entries',
        selector: '#iconBooleanColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/simple': {
        url: 'infolists/entries',
        selector: '#image',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/square': {
        url: 'infolists/entries',
        selector: '#imageSquare',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/circular': {
        url: 'infolists/entries',
        selector: '#imageCircular',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/stacked': {
        url: 'infolists/entries',
        selector: '#imageStacked',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/limited': {
        url: 'infolists/entries',
        selector: '#imageLimited',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/limited-remaining-text': {
        url: 'infolists/entries',
        selector: '#imageLimitedRemainingText',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/limited-remaining-text-separately': {
        url: 'infolists/entries',
        selector: '#imageLimitedRemainingTextSeparately',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/color/simple': {
        url: 'infolists/entries',
        selector: '#color',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/color/copyable': {
        url: 'infolists/entries',
        selector: '#colorCopyable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#colorCopyable .fi-in-color-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'infolists/entries/repeatable/simple': {
        url: 'infolists/entries',
        selector: '#repeatable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/repeatable/grid': {
        url: 'infolists/entries',
        selector: '#repeatableGrid',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/actions/suffix': {
        url: 'infolists/entries',
        selector: '#suffixAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/actions/hint': {
        url: 'infolists/entries',
        selector: '#hintAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/fieldset/simple': {
        url: 'infolists/layout',
        selector: '#fieldset',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/tabs/simple': {
        url: 'infolists/layout',
        selector: '#tabs',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/tabs/icons': {
        url: 'infolists/layout',
        selector: '#tabsIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/tabs/icons-after': {
        url: 'infolists/layout',
        selector: '#tabsIconsAfter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/tabs/badges': {
        url: 'infolists/layout',
        selector: '#tabsBadges',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/section/simple': {
        url: 'infolists/layout',
        selector: '#section',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/section/icons': {
        url: 'infolists/layout',
        selector: '#sectionIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/section/aside': {
        url: 'infolists/layout',
        selector: '#sectionAside',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/section/collapsed': {
        url: 'infolists/layout',
        selector: '#sectionCollapsed',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/section/compact': {
        url: 'infolists/layout',
        selector: '#sectionCompact',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/section/without-header': {
        url: 'infolists/layout',
        selector: '#sectionWithoutHeader',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/split/simple': {
        url: 'infolists/layout',
        selector: '#split',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/actions/anonymous/simple': {
        url: 'infolists/layout',
        selector: '#anonymousActions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/actions/anonymous/full-width': {
        url: 'infolists/layout',
        selector: '#anonymousActionsFullWidth',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/actions/anonymous/horizontally-aligned-center': {
        url: 'infolists/layout',
        selector: '#anonymousActionsHorizontallyAlignedCenter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/layout/actions/anonymous/vertically-aligned-end': {
        url: 'infolists/layout',
        selector: '#anonymousActionsVerticallyAlignedEnd',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'notifications/success': {
        url: 'notifications?method=success',
        selector: 'body',
        viewport: {
            width: 420,
            height: 90,
            deviceScaleFactor: 3,
        },
    },
    'notifications/icon': {
        url: 'notifications?method=icon',
        selector: 'body',
        viewport: {
            width: 420,
            height: 90,
            deviceScaleFactor: 3,
        },
    },
    'notifications/statuses': {
        url: 'notifications?method=statuses',
        selector: 'body',
        viewport: {
            width: 420,
            height: 295,
            deviceScaleFactor: 3,
        },
    },
    'notifications/color': {
        url: 'notifications?method=color',
        selector: 'body',
        viewport: {
            width: 420,
            height: 90,
            deviceScaleFactor: 3,
        },
    },
    'notifications/body': {
        url: 'notifications?method=body',
        selector: 'body',
        viewport: {
            width: 420,
            height: 115,
            deviceScaleFactor: 3,
        },
    },
    'notifications/actions': {
        url: 'notifications?method=actions',
        selector: 'body',
        viewport: {
            width: 420,
            height: 155,
            deviceScaleFactor: 3,
        },
    },
    'notifications/database': {
        url: 'notifications?method=openDatabaseNotifications',
        selector: 'body',
        viewport: {
            width: 860,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/example': {
        url: 'tables?table=example',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/getting-started/columns': {
        url: 'tables?table=gettingStartedColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/getting-started/searchable-columns': {
        url: 'tables?table=gettingStartedSearchableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/getting-started/sortable-columns': {
        url: 'tables?table=gettingStartedSortableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/getting-started/relationship-columns': {
        url: 'tables?table=gettingStartedRelationshipColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/getting-started/filters': {
        url: 'tables?table=gettingStartedFilters',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-filters-dropdown button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/getting-started/actions': {
        url: 'tables?table=gettingStartedActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/getting-started/actions-modal': {
        url: 'tables?table=gettingStartedActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('thead input')
            await page.click('.fi-dropdown-trigger')
            await page.click('.fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/columns/sortable': {
        url: 'tables?table=sortableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/searchable': {
        url: 'tables?table=searchableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/individually-searchable': {
        url: 'tables?table=individuallySearchableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/placeholder': {
        url: 'tables?table=placeholderColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/toggleable': {
        url: 'tables?table=toggleableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-col-toggle button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        }
    },
    'tables/columns/tooltips': {
        url: 'tables?table=columnTooltips',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('[wire\\:key$="4.column.email_verified_at"] .fi-ta-col-wrp')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/columns/alignment': {
        url: 'tables?table=columnAlignment',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/simple': {
        url: 'tables?table=textColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/badge': {
        url: 'tables?table=textColumnBadge',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/description': {
        url: 'tables?table=textColumnDescription',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/description-above': {
        url: 'tables?table=textColumnDescriptionAbove',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/color': {
        url: 'tables?table=textColumnColor',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/icon': {
        url: 'tables?table=textColumnIcon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/icon-after': {
        url: 'tables?table=textColumnIconAfter',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/large': {
        url: 'tables?table=textColumnLarge',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/bold': {
        url: 'tables?table=textColumnBold',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/mono': {
        url: 'tables?table=textColumnMono',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/copyable': {
        url: 'tables?table=textColumnCopyable',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('[wire\\:key$="4.column.email"] .fi-ta-text-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/columns/icon/simple': {
        url: 'tables?table=iconColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/icon/color': {
        url: 'tables?table=iconColumnColor',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/icon/medium': {
        url: 'tables?table=iconColumnMedium',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/icon/boolean': {
        url: 'tables?table=iconColumnBoolean',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/icon/boolean-icon': {
        url: 'tables?table=iconColumnBooleanIcon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/icon/boolean-color': {
        url: 'tables?table=iconColumnBooleanColor',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/simple': {
        url: 'tables?table=imageColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/square': {
        url: 'tables?table=imageColumnSquare',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/circular': {
        url: 'tables?table=imageColumnCircular',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/stacked': {
        url: 'tables?table=imageColumnStacked',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/limited': {
        url: 'tables?table=imageColumnLimited',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/limited-remaining-text': {
        url: 'tables?table=imageColumnLimitedRemainingText',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/limited-remaining-text-separately': {
        url: 'tables?table=imageColumnLimitedRemainingTextSeparately',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/color/simple': {
        url: 'tables?table=colorColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/color/copyable': {
        url: 'tables?table=colorColumnCopyable',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('[wire\\:key$="4.column.color"] .fi-ta-color-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/columns/select/simple': {
        url: 'tables?table=selectColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/toggle/simple': {
        url: 'tables?table=toggleColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text-input/simple': {
        url: 'tables?table=textInputColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/checkbox/simple': {
        url: 'tables?table=checkboxColumn',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/filters/simple': {
        url: 'tables?table=filters',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-filters-dropdown button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/filters/toggle': {
        url: 'tables?table=filtersToggle',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-filters-dropdown button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/filters/select': {
        url: 'tables?table=filtersSelect',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-filters-dropdown button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/filters/custom-form': {
        url: 'tables?table=filtersCustomForm',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-filters-dropdown button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/filters/indicators': {
        url: 'tables?table=filtersIndicators',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/filters/above-content': {
        url: 'tables?table=filtersAboveContent',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/filters/below-content': {
        url: 'tables?table=filtersBelowContent',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/filters/custom-trigger-action': {
        url: 'tables?table=filtersCustomTriggerAction',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/actions/simple': {
        url: 'tables?table=actions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/actions/before-columns': {
        url: 'tables?table=actionsBeforeColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/actions/before-cells': {
        url: 'tables?table=actionsBeforeCells',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/actions/bulk': {
        url: 'tables?table=bulkActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('thead input')
            await page.click('.fi-dropdown-trigger')
            await page.click('.fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/bulk-not-grouped': {
        url: 'tables?table=bulkActionsNotGrouped',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('thead input')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/header': {
        url: 'tables?table=headerActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/actions/group': {
        url: 'tables?table=groupedActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-icon-button': {
        url: 'tables?table=groupedActionsIconButton',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-button': {
        url: 'tables?table=groupedActionsButton',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-link': {
        url: 'tables?table=groupedActionsLink',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-icon': {
        url: 'tables?table=groupedActionsIcon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-color': {
        url: 'tables?table=groupedActionsColor',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-small': {
        url: 'tables?table=groupedActionsSmall',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/actions/group-tooltip': {
        url: 'tables?table=groupedActionsTooltip',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/layout/demo': {
        url: 'tables?table=layoutDemo',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/demo/mobile': {
        url: 'tables?table=layoutDemo',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-content .fi-icon-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1200, left: 0, top: 0 })
        },
    },
    'tables/layout/split': {
        url: 'tables?table=layoutSplit',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/split/mobile': {
        url: 'tables?table=layoutSplit',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1000, left: 0, top: 0 })
        },
    },
    'tables/layout/split-desktop': {
        url: 'tables?table=layoutSplitDesktop',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/split-desktop/mobile': {
        url: 'tables?table=layoutSplitDesktop',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1000, left: 0, top: 0 })
        },
    },
    'tables/layout/grow-disabled': {
        url: 'tables?table=layoutGrowDisabled',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/grow-disabled/mobile': {
        url: 'tables?table=layoutGrowDisabled',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1000, left: 0, top: 0 })
        },
    },
    'tables/layout/stack': {
        url: 'tables?table=layoutStack',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/stack/mobile': {
        url: 'tables?table=layoutStack',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1200, left: 0, top: 0 })
        },
    },
    'tables/layout/stack-hidden-on-mobile': {
        url: 'tables?table=layoutStackHiddenOnMobile',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/stack-hidden-on-mobile/mobile': {
        url: 'tables?table=layoutStackHiddenOnMobile',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1000, left: 0, top: 0 })
        },
    },
    'tables/layout/stack-aligned-right': {
        url: 'tables?table=layoutStackAlignedRight',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/collapsible': {
        url: 'tables?table=layoutCollapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-content .fi-icon-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/layout/collapsible/mobile': {
        url: 'tables?table=layoutCollapsible',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-content .fi-icon-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1200, left: 0, top: 0 })
        },
    },
    'tables/layout/grid': {
        url: 'tables?table=layoutGrid',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/grid/mobile': {
        url: 'tables?table=layoutGrid',
        selector: 'body',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1200, left: 0, top: 0 })
        },
    },
    'tables/summaries': {
        url: 'tables?table=summaries',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/grouping': {
        url: 'tables?table=grouping',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/grouping-descriptions': {
        url: 'tables?table=groupingDescriptions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/empty-state': {
        url: 'tables?table=emptyState',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/empty-state-heading': {
        url: 'tables?table=emptyStateHeading',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/empty-state-description': {
        url: 'tables?table=emptyStateDescription',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/empty-state-icon': {
        url: 'tables?table=emptyStateIcon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/empty-state-actions': {
        url: 'tables?table=emptyStateActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/reordering': {
        url: 'tables?table=reordering',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-icon-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/reordering/custom-trigger-action': {
        url: 'tables?table=reorderingCustomTriggerAction',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/striped': {
        url: 'tables?table=striped',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
}
