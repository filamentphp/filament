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
        url: 'forms',
        selector: '#simple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/helper-text': {
        url: 'forms',
        selector: '#helperText',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/hint': {
        url: 'forms',
        selector: '#hint',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/hint-color': {
        url: 'forms',
        selector: '#hintColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/hint-icon': {
        url: 'forms',
        selector: '#hintIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/disabled': {
        url: 'forms',
        selector: '#disabled',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/placeholder': {
        url: 'forms',
        selector: '#placeholder',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/simple': {
        url: 'forms',
        selector: '#textInput',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/affix': {
        url: 'forms',
        selector: '#textInputAffix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/suffix-icon': {
        url: 'forms',
        selector: '#textInputSuffixIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/simple': {
        url: 'forms',
        selector: '#select',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/searchable': {
        url: 'forms',
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
        url: 'forms',
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
        url: 'forms',
        selector: '#createSelectOption',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/create-option-modal': {
        url: 'forms',
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
        url: 'forms',
        selector: '#editSelectOption',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/edit-option-modal': {
        url: 'forms',
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
        url: 'forms',
        selector: '#selectAffix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/suffix-icon': {
        url: 'forms',
        selector: '#selectSuffixIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox/simple': {
        url: 'forms',
        selector: '#checkbox',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox/inline': {
        url: 'forms',
        selector: '#inlineCheckbox',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox/not-inline': {
        url: 'forms',
        selector: '#notInlineCheckbox',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/simple': {
        url: 'forms',
        selector: '#toggle',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/icons': {
        url: 'forms',
        selector: '#toggleIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/off-color': {
        url: 'forms',
        selector: '#toggleOffColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/on-color': {
        url: 'forms',
        selector: '#toggleOnColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/inline': {
        url: 'forms',
        selector: '#inlineToggle',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle/not-inline': {
        url: 'forms',
        selector: '#notInlineToggle',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/simple': {
        url: 'forms',
        selector: '#checkboxList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/columns': {
        url: 'forms',
        selector: '#checkboxListColumns',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/rows': {
        url: 'forms',
        selector: '#checkboxListRows',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/searchable': {
        url: 'forms',
        selector: '#searchableCheckboxList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/checkbox-list/bulk-toggleable': {
        url: 'forms',
        selector: '#bulkToggleableCheckboxList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/simple': {
        url: 'forms',
        selector: '#radio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/option-descriptions': {
        url: 'forms',
        selector: '#radioOptionDescriptions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/boolean': {
        url: 'forms',
        selector: '#booleanRadio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/inline': {
        url: 'forms',
        selector: '#inlineRadio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/radio/disabled-option': {
        url: 'forms',
        selector: '#disabledOptionRadio',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/simple': {
        url: 'forms',
        selector: '#dateTimePickers',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/without-seconds': {
        url: 'forms',
        selector: '#dateTimePickerWithoutSeconds',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/javascript': {
        url: 'forms',
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
        url: 'forms',
        selector: '#dateTimePickerDisplayFormat',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/week-starts-on-sunday': {
        url: 'forms',
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
        url: 'forms',
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
        url: 'forms',
        selector: '#dateTimePickerAffix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/prefix-icon': {
        url: 'forms',
        selector: '#dateTimePickerPrefixIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/file-upload/simple': {
        url: 'forms',
        selector: '#fileUpload',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/rich-editor/simple': {
        url: 'forms',
        selector: '#richEditor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/markdown-editor/simple': {
        url: 'forms',
        selector: '#markdownEditor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#markdownEditor .filament-forms-markdown-editor-component').scrollIntoView()
            })

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
}
