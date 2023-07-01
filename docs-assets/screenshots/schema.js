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
    'actions/trigger-button/indicator': {
        url: 'actions',
        selector: '#indicatorAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#indicatorAction button')
        },
    },
    'actions/trigger-button/success-indicator': {
        url: 'actions',
        selector: '#successIndicatorAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#successIndicatorAction button')
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
    'actions/trigger-button/inline-icon': {
        url: 'actions',
        selector: '#inlineIconAction',
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
            await page.waitForSelector('#actionGroup .filament-dropdown-list')

            await page.hover('#actionGroup .filament-dropdown-list-item')

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
            await page.waitForSelector('#customizedActionGroup .filament-dropdown-list')

            await page.hover('#customizedActionGroup .filament-dropdown-list-item')

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
            await page.waitForSelector('#actionGroupPlacement .filament-dropdown-list')

            await page.hover('#actionGroupPlacement .filament-dropdown-list-item')

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
            await page.waitForSelector('#nestedActionGroups .filament-dropdown-list')

            await page.hover('#nestedActionGroups .filament-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'app/dashboard': {
        url: 'admin',
        selector: 'body',
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
    'forms/fields/repeater/inset': {
        url: 'forms/fields',
        selector: '#insetRepeater',
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
            await page.click('#builderIcons .filament-forms-builder-component-block-picker button')

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
    'forms/fields/builder/inset': {
        url: 'forms/fields',
        selector: '#insetBuilder',
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
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#colorPicker').scrollIntoView()
            })

            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'forms/layout/placeholder/simple': {
        url: 'forms/layout',
        selector: '#placeholder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/layout/card/simple': {
        url: 'forms/layout',
        selector: '#card',
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
            await page.hover('#tooltips .filament-infolists-text-entry')

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
            await page.click('#textCopyable .filament-infolists-text-entry-content')

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
            await page.click('#colorCopyable .filament-infolists-color-entry-content')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
}
