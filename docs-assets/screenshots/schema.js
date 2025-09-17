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
        selector: '.fi-modal-window-ctn',
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
        selector: '.fi-modal-window-ctn',
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
        selector: '.fi-modal-window-ctn',
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
        selector: '.fi-modal-window-ctn',
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
        selector: '.fi-modal-window-ctn',
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
        selector: '.fi-modal-window-ctn',
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
    'actions/group/button-group': {
        url: 'actions',
        selector: '#actionButtonGroup',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
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
    'forms/fields/simple': {
        url: 'forms/fields',
        selector: '#simple',
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
    'forms/fields/inline-label': {
        url: 'forms/fields',
        selector: '#inlineLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/inline-label/section': {
        url: 'forms/fields',
        selector: '#inlineLabelSection',
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
    'forms/fields/fused': {
        url: 'forms/fields',
        selector: '#fused',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/fused-label': {
        url: 'forms/fields',
        selector: '#fusedLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/fused-columns': {
        url: 'forms/fields',
        selector: '#fusedColumns',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/fused-columns-span': {
        url: 'forms/fields',
        selector: '#fusedColumnsSpan',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-content/text': {
        url: 'forms/fields',
        selector: '#textBelowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-content/component': {
        url: 'forms/fields',
        selector: '#componentBelowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-content/action': {
        url: 'forms/fields',
        selector: '#actionBelowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-content': {
        url: 'forms/fields',
        selector: '#belowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-content/alignment': {
        url: 'forms/fields',
        selector: '#belowContentAlignment',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/above-label': {
        url: 'forms/fields',
        selector: '#aboveLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/before-label': {
        url: 'forms/fields',
        selector: '#beforeLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/after-label': {
        url: 'forms/fields',
        selector: '#afterLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/after-label/aligned-start': {
        url: 'forms/fields',
        selector: '#afterLabelAlignedStart',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-label': {
        url: 'forms/fields',
        selector: '#belowLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/above-content': {
        url: 'forms/fields',
        selector: '#aboveContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/before-content': {
        url: 'forms/fields',
        selector: '#beforeContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/after-content': {
        url: 'forms/fields',
        selector: '#afterContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/above-error-message': {
        url: 'forms/fields',
        selector: '#aboveErrorMessage',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/below-error-message': {
        url: 'forms/fields',
        selector: '#belowErrorMessage',
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
    'forms/fields/text-input/revealable-password': {
        url: 'forms/fields',
        selector: '#textInputRevealablePassword',
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
            await page.click('#javascriptSelect .fi-fo-select-btn')

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
            await page.click('#searchableSelect .fi-fo-select-btn')

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
            await page.click('#multipleSelect .fi-fo-select-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))

            await page.click('#multipleSelect .fi-fo-select-option')
            await page.click('#multipleSelect .fi-fo-select-option')

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
            await page.click('#groupedSelect .fi-fo-select-btn')

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
        selector: '.fi-modal-window-ctn',
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
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#editSelectOption button')

            await new Promise((resolve) => setTimeout(resolve, 500))

            await page.$eval('.fi-modal-window-ctn input[type=text]', (el) => el.blur())
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
    'forms/fields/repeater/table': {
        url: 'forms/fields',
        selector: '#repeaterTable',
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
    'forms/fields/toggle-buttons/simple': {
        url: 'forms/fields',
        selector: '#toggleButtons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/colors': {
        url: 'forms/fields',
        selector: '#toggleButtonsColors',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/icons': {
        url: 'forms/fields',
        selector: '#toggleButtonsIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/boolean': {
        url: 'forms/fields',
        selector: '#toggleButtonsBoolean',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/inline': {
        url: 'forms/fields',
        selector: '#toggleButtonsInline',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/grouped': {
        url: 'forms/fields',
        selector: '#toggleButtonsGrouped',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/multiple': {
        url: 'forms/fields',
        selector: '#toggleButtonsMultiple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/columns': {
        url: 'forms/fields',
        selector: '#toggleButtonsColumns',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/rows': {
        url: 'forms/fields',
        selector: '#toggleButtonsRows',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/toggle-buttons/disabled-option': {
        url: 'forms/fields',
        selector: '#disabledOptionToggleButtons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/simple': {
        url: 'forms/fields',
        selector: '#slider',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/range': {
        url: 'forms/fields',
        selector: '#sliderRange',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/multiple': {
        url: 'forms/fields',
        selector: '#sliderMultiple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/vertical': {
        url: 'forms/fields',
        selector: '#sliderVertical',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/tooltips': {
        url: 'forms/fields',
        selector: '#sliderTooltips',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/tooltips-multiple': {
        url: 'forms/fields',
        selector: '#sliderTooltipsMultiple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/tooltips-vertical': {
        url: 'forms/fields',
        selector: '#sliderTooltipsVertical',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/tooltips-formatting': {
        url: 'forms/fields',
        selector: '#sliderTooltipsFormatting',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/fill': {
        url: 'forms/fields',
        selector: '#sliderFill',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/fill-multiple': {
        url: 'forms/fields',
        selector: '#sliderFillMultiple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/fill-vertical': {
        url: 'forms/fields',
        selector: '#sliderFillVertical',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips': {
        url: 'forms/fields',
        selector: '#sliderPips',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-multiple': {
        url: 'forms/fields',
        selector: '#sliderPipsMultiple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-vertical': {
        url: 'forms/fields',
        selector: '#sliderPipsVertical',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-density': {
        url: 'forms/fields',
        selector: '#sliderPipsDensity',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-formatting': {
        url: 'forms/fields',
        selector: '#sliderPipsFormatting',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-steps': {
        url: 'forms/fields',
        selector: '#sliderPipsSteps',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-steps-density': {
        url: 'forms/fields',
        selector: '#sliderPipsStepsDensity',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-positions': {
        url: 'forms/fields',
        selector: '#sliderPipsPositions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-count': {
        url: 'forms/fields',
        selector: '#sliderPipsCount',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-values': {
        url: 'forms/fields',
        selector: '#sliderPipsValues',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-values-density': {
        url: 'forms/fields',
        selector: '#sliderPipsValuesDensity',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/pips-filter': {
        url: 'forms/fields',
        selector: '#sliderPipsFilter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/slider/non-linear': {
        url: 'forms/fields',
        selector: '#sliderNonLinear',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/code-editor/simple': {
        url: 'forms/fields',
        selector: '#codeEditor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForNetworkIdle()

            const el = await page.locator('#codeEditorLanguage').waitHandle()
            await el.scrollIntoView()

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/code-editor/language': {
        url: 'forms/fields',
        selector: '#codeEditorLanguage',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForNetworkIdle()

            const el = await page.locator('#codeEditorLanguage').waitHandle()
            await el.scrollIntoView()

            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'forms/overview': {
        url: 'forms/overview',
        selector: '#account-settings',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/overview/example': {
        url: 'schemas/overview',
        selector: '#example',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/fieldset/simple': {
        url: 'schemas/layout',
        selector: '#fieldset',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/fieldset/not-contained': {
        url: 'schemas/layout',
        selector: '#fieldsetNotContained',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/simple': {
        url: 'schemas/layout',
        selector: '#tabs',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/icons': {
        url: 'schemas/layout',
        selector: '#tabsIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/icons-after': {
        url: 'schemas/layout',
        selector: '#tabsIconsAfter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/badges': {
        url: 'schemas/layout',
        selector: '#tabsBadges',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/badges-color': {
        url: 'schemas/layout',
        selector: '#tabsBadgesColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/vertical': {
        url: 'schemas/layout',
        selector: '#tabsVertical',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/wizard/simple': {
        url: 'schemas/layout',
        selector: '#wizard',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/wizard/icons': {
        url: 'schemas/layout',
        selector: '#wizardIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/wizard/completed-icons': {
        url: 'schemas/layout',
        selector: '#wizardCompletedIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/wizard/descriptions': {
        url: 'schemas/layout',
        selector: '#wizardDescriptions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/simple': {
        url: 'schemas/layout',
        selector: '#section',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/header/actions': {
        url: 'schemas/layout',
        selector: '#sectionHeaderActions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/footer/actions': {
        url: 'schemas/layout',
        selector: '#sectionFooterActions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/icons': {
        url: 'schemas/layout',
        selector: '#sectionIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/aside': {
        url: 'schemas/layout',
        selector: '#sectionAside',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/collapsed': {
        url: 'schemas/layout',
        selector: '#sectionCollapsed',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/compact': {
        url: 'schemas/layout',
        selector: '#sectionCompact',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/secondary': {
        url: 'schemas/layout',
        selector: '#sectionSecondary',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/section/without-header': {
        url: 'schemas/layout',
        selector: '#sectionWithoutHeader',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/flex/simple': {
        url: 'schemas/layout',
        selector: '#flex',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/actions/independent/simple': {
        url: 'schemas/layout',
        selector: '#independentActions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/actions/independent/full-width': {
        url: 'schemas/layout',
        selector: '#independentActionsFullWidth',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/actions/independent/horizontally-aligned-center': {
        url: 'schemas/layout',
        selector: '#independentActionsHorizontallyAlignedCenter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/actions/independent/vertically-aligned-end': {
        url: 'schemas/layout',
        selector: '#independentActionsVerticallyAlignedEnd',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/overview': {
        url: 'infolists/overview',
        selector: '#product_info',
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
    'infolists/entries/inline-label': {
        url: 'infolists/entries',
        selector: '#inlineLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/inline-label/section': {
        url: 'infolists/entries',
        selector: '#inlineLabelSection',
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
    'infolists/entries/below-content/text': {
        url: 'infolists/entries',
        selector: '#textBelowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/below-content/component': {
        url: 'infolists/entries',
        selector: '#componentBelowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/below-content/action': {
        url: 'infolists/entries',
        selector: '#actionBelowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/below-content': {
        url: 'infolists/entries',
        selector: '#belowContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/below-content/alignment': {
        url: 'infolists/entries',
        selector: '#belowContentAlignment',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/above-label': {
        url: 'infolists/entries',
        selector: '#aboveLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/before-label': {
        url: 'infolists/entries',
        selector: '#beforeLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/after-label': {
        url: 'infolists/entries',
        selector: '#afterLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/after-label/aligned-start': {
        url: 'infolists/entries',
        selector: '#afterLabelAlignedStart',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/below-label': {
        url: 'infolists/entries',
        selector: '#belowLabel',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/above-content': {
        url: 'infolists/entries',
        selector: '#aboveContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/before-content': {
        url: 'infolists/entries',
        selector: '#beforeContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/after-content': {
        url: 'infolists/entries',
        selector: '#afterContent',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
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
    'infolists/entries/text/icon-color': {
        url: 'infolists/entries',
        selector: '#textIconColor',
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
    'infolists/entries/code/simple': {
        url: 'infolists/entries',
        selector: '#code',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/code/dracula': {
        url: 'infolists/entries',
        selector: '#codeDracula',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/key-value/simple': {
        url: 'infolists/entries',
        selector: '#keyValue',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
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
    'panels/navigation/user-menu': {
        url: 'panels/navigation/user-menu-customization',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 1080, height: 540, left: 1080, top: 0 })
        },
        before: async (page) => {
            await page.click('.fi-user-menu button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/navigation/disabled-navigation': {
        url: 'panels/navigation/disabled-navigation',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 1080, height: 540, left: 0, top: 0 })
        },
    },
    'panels/navigation/active-icon': {
        url: 'panels/navigation/active-icon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 1080, height: 540, left: 0, top: 0 })
        },
    },
    'panels/navigation/change-icon': {
        url: 'panels/navigation/change-icon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 600, height: 440, left: 0, top: 0 })
        },
    },
    'panels/navigation/custom-items': {
        url: 'panels/navigation/custom-items',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 540, left: 0, top: 0 })
        },
    },
    'panels/navigation/sidebar-collapsible-on-desktop': {
        url: 'panels/navigation/sidebar-collapsible-on-desktop',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 540, left: 0, top: 0 })
        },
        before: async (page) => {
            await page.click('.fi-topbar-close-collapse-sidebar-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/navigation/sidebar-fully-collapsible-on-desktop': {
        url: 'panels/navigation/sidebar-fully-collapsible-on-desktop',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 300, left: 0, top: 0 })
        },
        before: async (page) => {
            await page.click('.fi-topbar-close-collapse-sidebar-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/navigation/sort-items': {
        url: 'panels/navigation/sort-items',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 500, left: 0, top: 0 })
        },
    },
    'panels/navigation/top-navigation': {
        url: 'panels/navigation/top-navigation',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 1000, height: 300, left: 0, top: 0 })
        },
        before: async (page) => {
            await page.click('.fi-dropdown-trigger')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/navigation/badge': {
        url: 'panels/navigation/badge',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 500, left: 0, top: 0 })
        },
    },
    'panels/navigation/badge-color': {
        url: 'panels/navigation/badge-color',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 500, left: 0, top: 0 })
        },
    },
    'panels/navigation/badge-tooltip': {
        url: 'panels/navigation/badge-tooltip',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 750, height: 500, left: 0, top: 0 })
        },
        before: async (page) => {
            await page.hover('.fi-badge')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/navigation/group': {
        url: 'panels/navigation/group',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 550, left: 0, top: 0 })
        },
    },
    'panels/navigation/group-collapsible': {
        url: 'panels/navigation/group-collapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 550, left: 0, top: 0 })
        },
    },
    'panels/navigation/group-not-collapsible': {
        url: 'panels/navigation/group-not-collapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 0,
        },
        crop: (image) => {
            return image.extract({ width: 650, height: 550, left: 0, top: 0 })
        },
    },
    'primes/overview/example': {
        url: 'primes',
        selector: '#example',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/simple': {
        url: 'primes',
        selector: '#text',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/html': {
        url: 'primes',
        selector: '#textHtml',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/color': {
        url: 'primes',
        selector: '#textColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/neutral': {
        url: 'primes',
        selector: '#textNeutral',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/badge': {
        url: 'primes',
        selector: '#textBadge',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/badge-icon': {
        url: 'primes',
        selector: '#textBadgeIcon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/large': {
        url: 'primes',
        selector: '#textLarge',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/bold': {
        url: 'primes',
        selector: '#textBold',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/mono': {
        url: 'primes',
        selector: '#textMono',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/text/tooltip': {
        url: 'primes',
        selector: '#textTooltip',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#textTooltip .fi-sc-text')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'primes/icon/simple': {
        url: 'primes',
        selector: '#icon',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/icon/color': {
        url: 'primes',
        selector: '#iconColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/icon/tooltip': {
        url: 'primes',
        selector: '#iconTooltip',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#iconTooltip .fi-sc-icon')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'primes/image/simple': {
        url: 'primes',
        selector: '#image',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/image/size': {
        url: 'primes',
        selector: '#imageSize',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/image/alignment': {
        url: 'primes',
        selector: '#imageAlignment',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/image/tooltip': {
        url: 'primes',
        selector: '#imageTooltip',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#imageTooltip .fi-sc-image')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'primes/unordered-list/simple': {
        url: 'primes',
        selector: '#unorderedList',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'primes/unordered-list/large': {
        url: 'primes',
        selector: '#unorderedListLarge',
        viewport: {
            width: 1920,
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
    'tables/overview/columns': {
        url: 'tables?table=gettingStartedColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/overview/searchable-columns': {
        url: 'tables?table=gettingStartedSearchableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/overview/sortable-columns': {
        url: 'tables?table=gettingStartedSortableColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/overview/relationship-columns': {
        url: 'tables?table=gettingStartedRelationshipColumns',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/overview/filters': {
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
    'tables/overview/actions': {
        url: 'tables?table=gettingStartedActions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/overview/actions-modal': {
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
    'tables/columns/column-manager': {
        url: 'tables?table=columnManager',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-col-manager-dropdown button')

            await new Promise((resolve) => setTimeout(resolve, 500))
        }
    },
    'tables/columns/column-manager-reorderable': {
        url: 'tables?table=columnManagerReorderable',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-col-manager-dropdown button')

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
            await page.hover('[wire\\:key$="4.column.email_verified_at"] .fi-icon')

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
    'tables/columns/vertical-alignment': {
        url: 'tables?table=columnVerticalAlignment',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/grouping': {
        url: 'tables?table=columnGrouping',
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
    'tables/columns/text/icon-color': {
        url: 'tables?table=textColumnIconColor',
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
    'tables/actions/toolbar': {
        url: 'tables?table=toolbarActions',
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
