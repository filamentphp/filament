export default {
    'actions/create-action/modal': {
        url: 'actions-crud',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 720,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 3240, height: 2160, left: 0, top: 0 })
        },
        before: async (page) => {
            await page.click('[wire\\:click*="mountAction(\'create\'"]')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
            await page.$eval('.fi-modal-window-ctn input', (el) => el.blur())
        },
    },
    'actions/edit-action/modal': {
        url: 'actions-crud',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 720,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 3240, height: 2160, left: 0, top: 0 })
        },
        before: async (page) => {
            const buttons = await page.$$('[wire\\:click*="mountAction(\'edit\'"]')
            if (buttons.length > 0) await buttons[0].click()
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
            await page.$eval('.fi-modal-window-ctn input', (el) => el.blur())
        },
    },
    'actions/view-action/modal': {
        url: 'actions-crud',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 720,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 3240, height: 2160, left: 0, top: 0 })
        },
        before: async (page) => {
            const buttons = await page.$$('[wire\\:click*="mountAction(\'view\'"]')
            if (buttons.length > 0) await buttons[0].click()
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/delete-action/modal': {
        url: 'actions-crud',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const buttons = await page.$$('[wire\\:click*="mountAction(\'delete\'"]')
            if (buttons.length > 0) await buttons[0].click()
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/replicate-action/modal': {
        url: 'actions-crud',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 550,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const buttons = await page.$$('[wire\\:click*="mountAction(\'replicate\'"]')
            if (buttons.length > 0) await buttons[0].click()
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
            await page.$eval('.fi-modal-window-ctn input', (el) => el.blur())
        },
    },
    'actions/force-delete-action/modal': {
        url: 'actions-crud',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const buttons = await page.$$('[wire\\:click*="mountAction(\'forceDelete\'"]')
            if (buttons.length > 0) await buttons[0].click()
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/restore-action/modal': {
        url: 'actions-crud',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const buttons = await page.$$('[wire\\:click*="mountAction(\'restore\'"]')
            if (buttons.length > 0) await buttons[0].click()
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
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
    'actions/trigger-button/disabled': {
        url: 'actions',
        selector: '#disabledAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
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
    'actions/modal/wide': {
        url: 'actions',
        viewport: {
            width: 1280,
            height: 400,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#wideModalAction button')
            await page.waitForSelector('#modal h2')
            await new Promise((resolve) => setTimeout(resolve, 500))
            await page.$eval('.fi-modal-window-ctn input', (el) => el.blur())
        },
        selector: '.fi-modal-window-ctn',
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
    'actions/modal/schema': {
        url: 'actions',
        viewport: {
            width: 1080,
            height: 480,
            deviceScaleFactor: 2,
        },
        before: async (page) => {
            await page.click('#modalSchemaAction button')
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
    'actions/modal/no-close-button': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#modalNoCloseButtonAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: '.fi-modal-window-ctn',
    },
    'actions/modal/disabled-form': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 420,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#disabledFormAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        selector: '.fi-modal-window-ctn',
    },
    'actions/modal/icon-color': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#modalIconColorAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: '.fi-modal-window-ctn',
    },
    'actions/modal/extra-footer-actions': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 380,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#extraFooterActionsAction button')
            await page.waitForSelector('#modal h2')
            await new Promise((resolve) => setTimeout(resolve, 500))

            // Blur any focused inputs
            await page.evaluate(() => {
                document.activeElement?.blur()
            })
            await new Promise((resolve) => setTimeout(resolve, 100))
        },
        selector: '.fi-modal-window-ctn',
    },
    'actions/modal/alignment': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#modalAlignmentAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: '.fi-modal-window-ctn',
    },
    'actions/modal/overlaying-child': {
        url: 'actions',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 620,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Open the parent slide-over action.
            await page.click('#overlayingChildModalAction button')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 1500))

            // Blur any focused inputs.
            await page.evaluate(() => {
                document.activeElement?.blur()
            })
            await new Promise((resolve) => setTimeout(resolve, 200))

            // Click the first Delete button (aria-label="Delete") inside the slide-over.
            await page.click('.fi-modal-window-ctn button[aria-label="Delete"]')
            await new Promise((resolve) => setTimeout(resolve, 1500))

            // Constrain body to viewport so only the modal overlay area is captured.
            await page.evaluate(() => {
                document.body.style.height = '100vh'
                document.body.style.overflow = 'hidden'

                // Make the child modal's overlay more transparent so the parent slide-over is visible underneath.
                const overlays = document.querySelectorAll('.fi-modal-close-overlay')
                if (overlays.length > 1) {
                    overlays[overlays.length - 1].style.opacity = '0.4'
                }
            })
        },
    },
    'actions/modal/sticky-header': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 480,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#stickyModalHeaderAction button')
            await page.waitForSelector('#modal h2')
            await new Promise((resolve) => setTimeout(resolve, 500))

            // Blur any focused inputs
            await page.$eval('.fi-modal-window-ctn input', (el) => el.blur())

            // Scroll modal content down to show sticky header effect
            await page.evaluate(() => {
                const modalContent = document.querySelector('.fi-modal-window-ctn .fi-modal-content')
                if (modalContent) {
                    modalContent.scrollTop = 120
                }
            })
            await new Promise((resolve) => setTimeout(resolve, 300))
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
    'actions/group/dropdown-width': {
        url: 'actions',
        selector: '#actionGroupDropdownWidth',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        selectorPadding: 32,
        before: async (page) => {
            await page.click('#actionGroupDropdownWidth button')
            await page.waitForSelector('#actionGroupDropdownWidth .fi-dropdown-list')

            await page.hover('#actionGroupDropdownWidth .fi-dropdown-list-item')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/group/max-height': {
        url: 'actions',
        selector: '#actionGroupMaxHeight',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        selectorPadding: 32,
        before: async (page) => {
            await page.click('#actionGroupMaxHeight button')
            await page.waitForSelector('#actionGroupMaxHeight .fi-dropdown-list')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/trigger-button/authorization-tooltip': {
        url: 'actions',
        selector: '#authorizationTooltipAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#authorizationTooltipAction button')

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
    'forms/fields/hidden-label': {
        url: 'forms/fields',
        selector: '#hiddenLabel',
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
    'forms/fields/text-input/suffix-icon-color': {
        url: 'forms/fields',
        selector: '#textInputSuffixIconColor',
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
    'forms/fields/text-input/mask': {
        url: 'forms/fields',
        selector: '#textInputMask',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/copyable': {
        url: 'forms/fields',
        selector: '#textInputCopyable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/text-input/color': {
        url: 'forms/fields',
        selector: '#textInputColor',
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
            await page.click('#javascriptSelect .fi-select-input-btn')

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
            await page.click('#searchableSelect .fi-select-input-btn')

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
            await page.click('#multipleSelect .fi-select-input-btn')

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
            await page.click('#groupedSelect .fi-select-input-btn')

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
    'forms/fields/select/suffix-icon-color': {
        url: 'forms/fields',
        selector: '#selectSuffixIconColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/select/boolean': {
        url: 'forms/fields',
        selector: '#selectBoolean',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/modal-table-select/simple': {
        url: 'forms/modal-table-select',
        selector: '#modalTableSelect',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/modal-table-select/modal': {
        url: 'forms/modal-table-select',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 1920,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#modalTableSelect').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
            await page.click('#modalTableSelect button[type="button"]')
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
    },
    'forms/fields/select/disabled-options': {
        url: 'forms/fields',
        selector: '#selectDisabledOptions',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForSelector('#selectDisabledOptions .fi-select-input-btn')
            await page.click('#selectDisabledOptions .fi-select-input-btn')

            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'forms/fields/select/html-labels': {
        url: 'forms/fields',
        selector: '#selectHtmlLabels',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#selectHtmlLabels .fi-select-input-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/select/truncate-labels': {
        url: 'forms/fields',
        selector: '#selectTruncateLabels',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#selectTruncateLabels').scrollIntoView()
            })

            await new Promise((resolve) => setTimeout(resolve, 500))

            await page.click('#selectTruncateLabels .fi-select-input-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'forms/fields/checkbox-list/html-labels': {
        url: 'forms/fields',
        selector: '#checkboxListHtmlLabels',
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
    'forms/fields/checkbox-list/disabled-options': {
        url: 'forms/fields',
        selector: '#checkboxListDisabledOptions',
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
    'forms/fields/radio/boolean-custom-labels': {
        url: 'forms/fields',
        selector: '#booleanRadioCustomLabels',
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
    'forms/fields/date-time-picker/prefix-icon-color': {
        url: 'forms/fields',
        selector: '#dateTimePickerPrefixIconColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/date-time-picker/default-focused-date': {
        url: 'forms/fields',
        selector: '#dateTimePickerDefaultFocusedDate',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#dateTimePickerDefaultFocusedDate button')
            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'forms/fields/file-upload/avatar': {
        url: 'forms/fields',
        selector: '#fileUploadAvatar',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/file-upload/image-preview': {
        url: 'forms/fields',
        selector: '#fileUploadImagePreview',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Scroll into view so FilePond initializes (lazy-loaded).
            await page.evaluate(() => {
                document.querySelector('#fileUploadImagePreview').scrollIntoView()
            })
            // Wait for FilePond to load the image preview.
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
    },
    'forms/fields/file-upload/multiple-grid': {
        url: 'forms/fields',
        selector: '#fileUploadMultipleGrid',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Scroll into view so FilePond initializes (lazy-loaded).
            await page.evaluate(() => {
                document.querySelector('#fileUploadMultipleGrid').scrollIntoView()
            })
            // Wait for FilePond to load image previews.
            await new Promise((resolve) => setTimeout(resolve, 3000))
        },
    },
    'forms/fields/file-upload/openable': {
        url: 'forms/fields',
        selector: '#fileUploadOpenable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#fileUploadOpenable').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
    },
    'forms/fields/file-upload/downloadable': {
        url: 'forms/fields',
        selector: '#fileUploadDownloadable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#fileUploadDownloadable').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
    },
    'forms/fields/file-upload/image-editor': {
        url: 'forms/fields',
        selector: '#fileUploadImageEditor .fi-fo-file-upload-editor-window',
        viewport: {
            width: 1920,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Scroll to file upload with image editor so FilePond initializes.
            await page.evaluate(() => {
                document.querySelector('#fileUploadImageEditor').scrollIntoView()
            })
            // Wait for FilePond to load the image.
            await new Promise((resolve) => setTimeout(resolve, 5000))

            // Click the edit (pencil) button on the uploaded file to open the image editor.
            await page.click('#fileUploadImageEditor .filepond--action-edit-item')
            await new Promise((resolve) => setTimeout(resolve, 3000))

            // Blur any focused inputs.
            await page.evaluate(() => {
                document.activeElement?.blur()
            })
            await new Promise((resolve) => setTimeout(resolve, 200))
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
    'forms/fields/rich-editor/custom-toolbar': {
        url: 'forms/fields',
        selector: '#richEditorCustomToolbar',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#richEditorCustomToolbar').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/rich-editor/toolbar-button-group-open': {
        url: 'forms/fields',
        selector: '#richEditorToolbarButtonGroup',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const triggers = await page.$$('#richEditorToolbarButtonGroup .fi-fo-rich-editor-dropdown-tool-trigger')
            await triggers[1].click()
            await page.waitForSelector('#richEditorToolbarButtonGroup .fi-fo-rich-editor-dropdown-tool-menu')
            await page.mouse.move(0, 0)
            await page.evaluate(() => document.activeElement.blur())
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/rich-editor/textual-toolbar-button-group-open': {
        url: 'forms/fields',
        selector: '#richEditorTextualToolbarButtonGroup',
        viewport: {
            width: 1920,
            height: 800,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#richEditorTextualToolbarButtonGroup .fi-fo-rich-editor-dropdown-tool-trigger')
            await page.waitForSelector('#richEditorTextualToolbarButtonGroup .fi-fo-rich-editor-dropdown-tool-menu')
            await page.mouse.move(0, 0)
            await page.evaluate(() => document.activeElement.blur())
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/rich-editor/merge-tags': {
        url: 'forms/fields',
        selector: '#richEditorMergeTags',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/rich-editor/text-colors': {
        url: 'forms/fields',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1920, height: 1920, left: 0, top: 0 })
        },
        before: async (page) => {
            // Scroll the rich editor into view so its JS initializes.
            await page.evaluate(() => {
                document.querySelector('#richEditorTextColors').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))

            // Click the text color toolbar button to open the action modal.
            await page.click('#richEditorTextColors button[aria-label="Text color"]')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))

            // Click the select input to open the dropdown showing color options.
            await page.click('.fi-modal-window-ctn .fi-select-input-btn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/rich-editor/mentions': {
        url: 'forms/fields',
        selector: '#richEditorMentions',
        viewport: {
            width: 1920,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Scroll the rich editor into view so its JS initializes.
            await page.evaluate(() => {
                document.querySelector('#richEditorMentions').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))

            // Click inside the TipTap editor content area.
            await page.click('#richEditorMentions .tiptap')
            await new Promise((resolve) => setTimeout(resolve, 300))

            // Type @ to trigger the mention dropdown.
            await page.keyboard.type('@')
            await new Promise((resolve) => setTimeout(resolve, 500))

            // Move the dropdown inside the editor container so it's captured in the screenshot.
            await page.evaluate(() => {
                const dropdown = document.querySelector('body > .fi-dropdown-panel')
                const container = document.querySelector('#richEditorMentions')
                if (dropdown && container) {
                    const containerRect = container.getBoundingClientRect()
                    const dropdownRect = dropdown.getBoundingClientRect()
                    container.style.paddingBottom = (dropdownRect.bottom - containerRect.bottom + 64) + 'px'
                }
            })
            await new Promise((resolve) => setTimeout(resolve, 100))
        },
    },
    'forms/fields/rich-editor/custom-blocks': {
        url: 'forms/fields',
        selector: '#richEditorCustomBlocks',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Scroll into view so JS initializes.
            await page.evaluate(() => {
                document.querySelector('#richEditorCustomBlocks').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))

            // Click the custom blocks toolbar button to open the side panel.
            await page.click('#richEditorCustomBlocks button[aria-label="Blocks"]')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/rich-editor/floating-toolbar': {
        url: 'forms/fields',
        selector: '#richEditorFloatingToolbar',
        viewport: {
            width: 1920,
            height: 800,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Scroll into view so JS initializes (lazy-loaded TipTap).
            await page.evaluate(() => {
                document.querySelector('#richEditorFloatingToolbar').scrollIntoView()
            })
            await new Promise((resolve) => setTimeout(resolve, 2000))

            // Click inside the editor to place cursor in the paragraph.
            const editor = await page.$('#richEditorFloatingToolbar .tiptap.ProseMirror')
            await editor.click()
            await new Promise((resolve) => setTimeout(resolve, 300))

            // Triple-click to select the paragraph text, triggering the floating bubble menu.
            await editor.click({ clickCount: 3 })
            await new Promise((resolve) => setTimeout(resolve, 800))

            // Remove the focus outline ring from the editor wrapper and add bottom padding
            // so the floating toolbar below the editor area is fully captured.
            await page.evaluate(() => {
                // Remove focus ring from the editor wrapper.
                const wrapper = document.querySelector('#richEditorFloatingToolbar .fi-fo-rich-editor')
                if (wrapper) wrapper.style.outline = 'none'
                const input = document.querySelector('#richEditorFloatingToolbar .fi-input-wrp')
                if (input) {
                    input.style.outline = 'none'
                    input.style.boxShadow = 'none'
                    input.style.ring = 'none'
                }
                // Also remove any ring on :focus-within elements.
                document.querySelectorAll('#richEditorFloatingToolbar *:focus, #richEditorFloatingToolbar *:focus-within').forEach((el) => {
                    el.style.outline = 'none'
                    el.style.boxShadow = el.style.boxShadow.replace(/0 0 0 [^ ]+ (rgb|var)[^,]*/g, '0 0 0 0px transparent')
                })
                // Add padding to container so floating toolbar is fully visible.
                const group = document.querySelector('#richEditorFloatingToolbar')
                if (group) group.style.paddingBottom = '5rem'
            })
            await new Promise((resolve) => setTimeout(resolve, 200))
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
    'forms/fields/markdown-editor/custom-toolbar': {
        url: 'forms/fields',
        selector: '#markdownEditorCustomToolbar',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#markdownEditorCustomToolbar').scrollIntoView()
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
    'forms/fields/repeater/table-compact': {
        url: 'forms/fields',
        selector: '#repeaterTableCompact',
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
    'forms/fields/repeater/collapsible': {
        url: 'forms/fields',
        selector: '#collapsibleRepeater',
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
    'forms/fields/repeater/numbered': {
        url: 'forms/fields',
        selector: '#numberedRepeater',
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
    'forms/fields/repeater/add-action-alignment': {
        url: 'forms/fields',
        selector: '#repeaterAddActionAlignment',
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
        crop: (image) => {
            return image.extract({ width: 3072, height: 2000, left: 0, top: 0 })
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
    'forms/fields/builder/block-icons': {
        url: 'forms/fields',
        selector: '#builderBlockIcons',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/block-previews': {
        url: 'forms/fields',
        selector: '#builderBlockPreviews',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/builder/add-action-alignment': {
        url: 'forms/fields',
        selector: '#builderAddActionAlignment',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
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
    'forms/fields/builder/collapsible': {
        url: 'forms/fields',
        selector: '#collapsibleBuilder',
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
    'forms/fields/builder/block-picker-columns': {
        url: 'forms/fields',
        selector: '#builderBlockPickerColumns',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.evaluate(() => {
                document.querySelector('#builderBlockPickerColumns').scrollIntoView()
            })

            await new Promise((resolve) => setTimeout(resolve, 500))

            await page.click('#builderBlockPickerColumns .fi-fo-builder-block-picker button')

            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'forms/fields/tags-input/tag-prefix': {
        url: 'forms/fields',
        selector: '#tagsInputTagPrefix',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/tags-input/color': {
        url: 'forms/fields',
        selector: '#tagsInputColor',
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
    'forms/fields/textarea/rows': {
        url: 'forms/fields',
        selector: '#textareaRows',
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
    'forms/fields/color-picker/panel': {
        url: 'forms/fields',
        selector: '#colorPickerOpen',
        viewport: {
            width: 1920,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#colorPickerOpen .fi-fo-color-picker-preview')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'forms/fields/color-picker/formats': {
        url: 'forms/fields',
        selector: '#colorPickerFormats',
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
    'forms/fields/toggle-buttons/hidden-labels': {
        url: 'forms/fields',
        selector: '#toggleButtonsHiddenLabels',
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
    'forms/fields/toggle-buttons/tooltips': {
        url: 'forms/fields',
        selector: '#toggleButtonsTooltips',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Hover over the "Published" button (label tag) to show its tooltip
            const labels = await page.$$('#toggleButtonsTooltips .fi-fo-toggle-buttons-btn-ctn label')
            if (labels.length > 2) await labels[2].hover()

            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'forms/fields/slider/top-to-bottom': {
        url: 'forms/fields',
        selector: '#sliderTopToBottom',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const el = await page.locator('#sliderTopToBottom').waitHandle()
            await el.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
    },
    'forms/fields/slider/range-padding': {
        url: 'forms/fields',
        selector: '#sliderRangePadding',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const el = await page.locator('#sliderRangePadding').waitHandle()
            await el.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
    },
    'forms/fields/slider/rtl': {
        url: 'forms/fields',
        selector: '#sliderRtl',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const el = await page.locator('#sliderRtl').waitHandle()
            await el.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 2000))
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
    'schemas/layout/dense': {
        url: 'schemas/layout',
        selector: '#dense',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/no-gap': {
        url: 'schemas/layout',
        selector: '#noGap',
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
    'schemas/layout/tabs/not-scrollable': {
        url: 'schemas/layout',
        selector: '#tabsNotScrollable',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/tabs/not-contained': {
        url: 'schemas/layout',
        selector: '#tabsNotContained',
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
    'schemas/layout/wizard/submit-action': {
        url: 'schemas/layout',
        selector: '#wizardSubmitAction',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/empty-state/simple': {
        url: 'schemas/layout',
        selector: '#emptyState',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/empty-state/contained-false': {
        url: 'schemas/layout',
        selector: '#emptyStateContainedFalse',
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
    'schemas/layout/section/columns': {
        url: 'schemas/layout',
        selector: '#sectionColumns',
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
    'schemas/layout/callout/simple': {
        url: 'schemas/layout',
        selector: '#callout',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/statuses': {
        url: 'schemas/layout',
        selector: '#calloutStatuses',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/without-background': {
        url: 'schemas/layout',
        selector: '#calloutWithoutBackground',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/custom-color': {
        url: 'schemas/layout',
        selector: '#calloutCustomColor',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/actions': {
        url: 'schemas/layout',
        selector: '#calloutActions',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/custom-icon': {
        url: 'schemas/layout',
        selector: '#calloutCustomIcon',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/icon-size': {
        url: 'schemas/layout',
        selector: '#calloutIconSize',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/footer': {
        url: 'schemas/layout',
        selector: '#calloutFooter',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/control-actions': {
        url: 'schemas/layout',
        selector: '#calloutControlActions',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/callout/actions-aligned-end': {
        url: 'schemas/layout',
        selector: '#calloutActionsAlignedEnd',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/grid/simple': {
        url: 'schemas/layout',
        selector: '#grid',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/grid/column-span': {
        url: 'schemas/layout',
        selector: '#gridColumnSpan',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/grid/column-start': {
        url: 'schemas/layout',
        selector: '#gridColumnStart',
        viewport: {
            width: 1920,
            height: 320,
            deviceScaleFactor: 3,
        },
    },
    'schemas/layout/grid/column-order': {
        url: 'schemas/layout',
        selector: '#gridColumnOrder',
        viewport: {
            width: 1920,
            height: 320,
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
    'infolists/entries/hidden-label': {
        url: 'infolists/entries',
        selector: '#hiddenLabel',
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
    'infolists/entries/alignment': {
        url: 'infolists/entries',
        selector: '#alignment',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
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
    'infolists/entries/text/separator-badge': {
        url: 'infolists/entries',
        selector: '#textSeparatorBadge',
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
    'infolists/entries/text/expandable-limited-list': {
        url: 'infolists/entries',
        selector: '#textExpandableLimitedList',
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
    'infolists/entries/text/limit': {
        url: 'infolists/entries',
        selector: '#textLimit',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/words': {
        url: 'infolists/entries',
        selector: '#textWords',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/wrap': {
        url: 'infolists/entries',
        selector: '#textWrap',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/line-clamp': {
        url: 'infolists/entries',
        selector: '#textLineClamp',
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
    'infolists/entries/text/numeric': {
        url: 'infolists/entries',
        selector: '#textNumeric',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/money': {
        url: 'infolists/entries',
        selector: '#textMoney',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/date': {
        url: 'infolists/entries',
        selector: '#textDate',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/since': {
        url: 'infolists/entries',
        selector: '#textSince',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/date-tooltip': {
        url: 'infolists/entries',
        selector: '#textDateTooltip',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('#textDateTooltip [x-tooltip]')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'infolists/entries/text/markdown': {
        url: 'infolists/entries',
        selector: '#textMarkdown',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/text/html': {
        url: 'infolists/entries',
        selector: '#textHtml',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
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
    'infolists/entries/image/size': {
        url: 'infolists/entries',
        selector: '#imageSize',
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
    'infolists/entries/image/stacked-ring': {
        url: 'infolists/entries',
        selector: '#imageStackedRing',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/image/stacked-overlap': {
        url: 'infolists/entries',
        selector: '#imageStackedOverlap',
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
    'infolists/entries/code/javascript': {
        url: 'infolists/entries',
        selector: '#codeJavascript',
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
    'infolists/entries/key-value/custom-labels': {
        url: 'infolists/entries',
        selector: '#keyValueCustomLabels',
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
    'infolists/entries/repeatable/contained-false': {
        url: 'infolists/entries',
        selector: '#repeatableContainedFalse',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'infolists/entries/repeatable/table': {
        url: 'infolists/entries',
        selector: '#repeatableTable',
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
    'notifications/positioning': {
        url: 'notifications?method=positioning',
        selector: 'body',
        viewport: {
            width: 560,
            height: 300,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForSelector('.fi-no-notification', { visible: true, timeout: 5000 }).catch(() => {})
            await new Promise((resolve) => setTimeout(resolve, 500))
            await page.evaluate(() => {
                const container = document.querySelector('.fi-no')
                if (container) {
                    container.classList.remove('fi-align-right', 'fi-align-center', 'fi-align-end', 'fi-vertical-align-start', 'fi-vertical-align-center')
                    container.classList.add('fi-align-start', 'fi-vertical-align-end')
                }
            })
            await new Promise((resolve) => setTimeout(resolve, 100))
        },
    },
    'panels/navigation/user-menu': {
        url: 'panels/navigation/user-menu-customization',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1620, height: 1050, left: 1620, top: 0 })
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
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 3240, height: 450, left: 0, top: 0 })
        },
    },
    'panels/navigation/active-icon': {
        url: 'panels/navigation/active-icon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 750, left: 0, top: 0 })
        },
    },
    'panels/navigation/change-icon': {
        url: 'panels/navigation/change-icon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1800, height: 750, left: 0, top: 0 })
        },
    },
    'panels/navigation/custom-items': {
        url: 'panels/navigation/custom-items',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 750, left: 0, top: 0 })
        },
    },
    'panels/navigation/sidebar-collapsible-on-desktop': {
        url: 'panels/navigation/sidebar-collapsible-on-desktop',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 900, height: 750, left: 0, top: 0 })
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
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 350, left: 0, top: 0 })
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
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 900, left: 0, top: 0 })
        },
    },
    'panels/navigation/top-navigation': {
        url: 'panels/navigation/top-navigation',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Click a navigation group button to open its dropdown
            const groupButtons = await page.$$('.fi-topbar-item:not(.fi-active) .fi-topbar-item-btn')
            if (groupButtons.length > 0) await groupButtons[0].click()
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 1080 * scale, height: 350 * scale, left: 0, top: 0 })
        },
    },
    'panels/navigation/badge': {
        url: 'panels/navigation/badge',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 900, left: 0, top: 0 })
        },
    },
    'panels/navigation/badge-color': {
        url: 'panels/navigation/badge-color',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 900, left: 0, top: 0 })
        },
    },
    'panels/navigation/badge-tooltip': {
        url: 'panels/navigation/badge-tooltip',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 2250, height: 900, left: 0, top: 0 })
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
            height: 800,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 1500, left: 0, top: 0 })
        },
    },
    'panels/navigation/group-collapsible': {
        url: 'panels/navigation/group-collapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 800,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 1200, left: 0, top: 0 })
        },
    },
    'panels/navigation/group-not-collapsible': {
        url: 'panels/navigation/group-not-collapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 800,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1950, height: 1350, left: 0, top: 0 })
        },
    },
    'panels/navigation/user-menu-sidebar': {
        url: 'panels/navigation/user-menu-sidebar',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 380,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 3240, height: 1140, left: 0, top: 0 })
        },
    },
    'panels/navigation/sidebar-collapsible-with-group-icons': {
        url: 'panels/navigation/sidebar-collapsible-with-group-icons',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 900, height: 750, left: 0, top: 0 })
        },
        before: async (page) => {
            await page.click('.fi-topbar-close-collapse-sidebar-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'tables/columns/column-manager-columns': {
        url: 'tables?table=columnManagerColumns',
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
    'tables/columns/column-manager-modal': {
        url: 'tables?table=columnManagerModal',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-modal-trigger .fi-icon-btn')

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
    'tables/columns/header-tooltips': {
        url: 'tables?table=columnHeaderTooltips',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('.fi-ta-header-cell-tooltip')

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
    'tables/columns/wrap-header': {
        url: 'tables?table=columnWrapHeader',
        selector: 'body',
        viewport: {
            width: 640,
            height: 480,
            deviceScaleFactor: 3,
        },
    },
    'tables/heading': {
        url: 'tables?table=tableHeading',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/width': {
        url: 'tables?table=columnWidth',
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
    'tables/columns/text/separator-badge': {
        url: 'tables?table=textColumnSeparatorBadge',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/markdown': {
        url: 'tables?table=textColumnMarkdown',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/html': {
        url: 'tables?table=textColumnHtml',
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
    'tables/columns/text/list': {
        url: 'tables?table=columnListWithLineBreaks',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/bulleted': {
        url: 'tables?table=columnBulleted',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/expandable-limited-list': {
        url: 'tables?table=columnExpandableLimitedList',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 1000))

            // Click the "Show 2 more" expand link on the second row via evaluate
            await page.evaluate(() => {
                const expandBtns = document.querySelectorAll('.fi-ta-text-list-limited-message [role="button"][x-on\\:click\\.prevent\\.stop="isLimited = false"]')
                if (expandBtns.length > 1) expandBtns[1].click()
            })
            await new Promise((resolve) => setTimeout(resolve, 500))
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
    'tables/columns/text/numeric': {
        url: 'tables?table=textColumnNumeric',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/money': {
        url: 'tables?table=textColumnMoney',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/date': {
        url: 'tables?table=textColumnDate',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/since': {
        url: 'tables?table=textColumnSince',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/date-tooltip': {
        url: 'tables?table=textColumnDateTooltip',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.hover('.fi-ta-text [x-tooltip]')

            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'tables/columns/text/limit': {
        url: 'tables?table=textColumnLimit',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/words': {
        url: 'tables?table=textColumnWords',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/wrap': {
        url: 'tables?table=textColumnWrap',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text/line-clamp': {
        url: 'tables?table=textColumnLineClamp',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
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
    'tables/columns/icon/wrap': {
        url: 'tables?table=iconColumnWrap',
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
    'tables/columns/image/size': {
        url: 'tables?table=imageColumnSize',
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
    'tables/columns/image/stacked-ring': {
        url: 'tables?table=imageColumnStackedRing',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/image/stacked-overlap': {
        url: 'tables?table=imageColumnStackedOverlap',
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
    'tables/columns/color/wrap': {
        url: 'tables?table=colorColumnWrap',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
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
    'tables/columns/select/javascript': {
        url: 'tables?table=selectColumnJavascript',
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
    'tables/columns/text-input/affix': {
        url: 'tables?table=textInputColumnAffix',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text-input/prefix-icon': {
        url: 'tables?table=textInputColumnPrefixIcon',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/columns/text-input/suffix-icon-color': {
        url: 'tables?table=textInputColumnSuffixIconColor',
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
    'tables/filters/multi-select': {
        url: 'tables?table=filtersMultiSelect',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 800,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Open filters dropdown
            await page.click('.fi-ta-filters-dropdown button')
            await new Promise((resolve) => setTimeout(resolve, 500))

            // Open the multi-select dropdown
            await page.click('.fi-select-input-btn')
            await new Promise((resolve) => setTimeout(resolve, 500))

            // Select "Reviewing" and "Published" options
            const options = await page.$$('.fi-select-dropdown-option')
            for (const option of options) {
                const text = await option.evaluate((el) => el.textContent.trim())
                if (text === 'Reviewing' || text === 'Published') {
                    await option.click()
                    await new Promise((resolve) => setTimeout(resolve, 300))
                }
            }

            // Re-open the dropdown to show it with selections made
            await page.click('.fi-select-input-btn')
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
    'tables/filters/before-content': {
        url: 'tables?table=filtersBeforeContent',
        selector: 'body',
        viewport: {
            width: 1280,
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
    'tables/filters/grid-columns': {
        url: 'tables?table=filtersGridColumns',
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
    'tables/filters/modal': {
        url: 'tables?table=filtersModal',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-ta-header-toolbar .fi-icon-btn')

            await new Promise((resolve) => setTimeout(resolve, 500))

            await page.evaluate(() => { document.activeElement?.blur() })

            await new Promise((resolve) => setTimeout(resolve, 100))
        },
    },
    'tables/filters/ternary': {
        url: 'tables?table=filtersTernary',
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
    'tables/filters/after-content': {
        url: 'tables?table=filtersAfterContent',
        selector: 'body',
        viewport: {
            width: 1280,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/filters/above-content-collapsible': {
        url: 'tables?table=filtersAboveContentCollapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/filters/custom-form-schema': {
        url: 'tables?table=filtersCustomFormSchema',
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
    'tables/filters/query-builder': {
        url: 'tables?table=filtersQueryBuilder',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 800,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Click "Add rule" to show constraint options
            const buttons = await page.$$('.fi-ac-btn-action')
            for (const button of buttons) {
                const text = await page.evaluate((el) => el.textContent.trim(), button)
                if (text.includes('Add rule')) {
                    await button.click()
                    await new Promise((resolve) => setTimeout(resolve, 1000))
                    break
                }
            }
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
    'tables/layout/stack-spaced': {
        url: 'tables?table=layoutStackSpaced',
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
    'tables/layout/column-grid': {
        url: 'tables?table=layoutColumnGrid',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
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
    'tables/layout/stacked-on-mobile': {
        url: 'tables?table=layoutStackedOnMobile',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/layout/stacked-on-mobile/mobile': {
        url: 'tables?table=layoutStackedOnMobile',
        selector: 'body',
        viewport: {
            width: 375,
            height: 812,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 1125, height: 1800, left: 0, top: 0 })
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
    'tables/grouping-selectable': {
        url: 'tables?table=groupingSelectable',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/grouping-date': {
        url: 'tables?table=groupingDate',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/grouping-groups-only': {
        url: 'tables?table=groupingGroupsOnly',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 400,
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
    'tables/grouping-collapsible': {
        url: 'tables?table=groupingCollapsible',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/summaries/average': {
        url: 'tables?table=summaryAverage',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/summaries/sum': {
        url: 'tables?table=summarySum',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/summaries/count': {
        url: 'tables?table=summaryCount',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/summaries/range': {
        url: 'tables?table=summaryRange',
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
    'tables/custom-row-classes': {
        url: 'tables?table=tableCustomRowClasses',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/stats-overview/simple': {
        url: 'widgets',
        selector: '#statsSimple',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/stats-overview/description': {
        url: 'widgets',
        selector: '#statsDescription',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/stats-overview/color': {
        url: 'widgets',
        selector: '#statsColor',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/stats-overview/chart': {
        url: 'widgets',
        selector: '#statsChart',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/stats-overview/heading': {
        url: 'widgets',
        selector: '#statsHeading',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/line': {
        url: 'widgets',
        selector: '#chartLine',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/bar': {
        url: 'widgets',
        selector: '#chartBar',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/description': {
        url: 'widgets',
        selector: '#chartDescription',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/filter': {
        url: 'widgets',
        selector: '#chartFilter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/pie': {
        url: 'widgets',
        selector: '#chartPie',
        viewport: {
            width: 1200,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/doughnut': {
        url: 'widgets',
        selector: '#chartDoughnut',
        viewport: {
            width: 1200,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/radar': {
        url: 'widgets',
        selector: '#chartRadar',
        viewport: {
            width: 1200,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/polar-area': {
        url: 'widgets',
        selector: '#chartPolarArea',
        viewport: {
            width: 1200,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/scatter': {
        url: 'widgets',
        selector: '#chartScatter',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/bubble': {
        url: 'widgets',
        selector: '#chartBubble',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'panels/login': {
        url: 'admin/login?no_auto_login=1',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 700,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            return image.extract({ width: 3240, height: 2100, left: 0, top: 0 })
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            // Blur the auto-focused email input to remove orange outline
            await page.$eval('input[type="email"]', (el) => el.blur())
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
    },
    'panels/registration': {
        url: 'admin/register?no_auto_login=1',
        selector: 'body',
        viewport: {
            width: 800,
            height: 720,
            deviceScaleFactor: 3,
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            await page.$eval('input[type="text"]', (el) => el.blur())
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        crop: (image) => {
            const scale = 3
            return image.extract({ width: 800 * scale, height: 700 * scale, left: 0, top: 0 })
        },
    },
    'panels/password-reset': {
        url: 'admin/password-reset/request?no_auto_login=1',
        selector: 'body',
        viewport: {
            width: 640,
            height: 500,
            deviceScaleFactor: 3,
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            await page.evaluate(() => document.activeElement?.blur())
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
    },
    'panels/profile': {
        url: 'admin/profile',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            await page.evaluate(() => document.activeElement?.blur())
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        crop: (image) => {
            const scale = 3
            return image.extract({ width: 1080 * scale, height: 640 * scale, left: 0, top: 0 })
        },
    },
    'panels/mfa': {
        url: 'admin/profile',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 1100,
            deviceScaleFactor: 3,
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            await page.evaluate(() => document.activeElement?.blur())
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        crop: (image) => {
            const scale = 3
            return image.extract({ width: 1080 * scale, height: 420 * scale, left: 0, top: 530 * scale })
        },
    },
    'panels/mfa-challenge': {
        url: 'admin/login?no_auto_login=1',
        selector: 'body',
        viewport: {
            width: 640,
            height: 500,
            deviceScaleFactor: 3,
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            // Fill in login credentials
            await page.type('input[type="email"]', 'dan@filamentphp.com')
            await page.type('input[type="password"]', 'password')
            // Submit the login form
            await page.click('button[type="submit"]')
            // Wait for MFA challenge form to appear
            await page.waitForSelector('[id="multiFactorChallengeForm"]', { timeout: 10000 })
            await new Promise((resolve) => setTimeout(resolve, 500))
            // Blur any focused element for clean screenshot
            await page.evaluate(() => document.activeElement?.blur())
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
    },
    'panels/dashboard': {
        url: 'admin',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/dashboard-filters': {
        url: 'admin/dashboard-filtered',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 920,
            deviceScaleFactor: 3,
        },
    },
    'panels/configuration/content-width-full': {
        url: 'admin?maxContentWidth=full',
        selector: 'body',
        viewport: {
            width: 1920,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/configuration/colors': {
        url: 'admin?primaryColor=blue',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/styling/colors': {
        url: 'admin?primaryColor=rose',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/styling/font': {
        url: 'admin?font=Poppins',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/styling/brand-name': {
        url: 'admin?brandName=Filament+Demo',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForFunction(() => {
                const logo = document.querySelector('.fi-logo');
                return logo && logo.offsetParent !== null;
            }, { timeout: 5000 });
            await new Promise((resolve) => setTimeout(resolve, 500));
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 500 * scale, height: 200 * scale, left: 0, top: 0 });
        },
    },
    'panels/styling/brand-logo': {
        url: 'admin?brandLogo=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForFunction(() => {
                const logo = document.querySelector('.fi-logo');
                return logo && logo.offsetParent !== null;
            }, { timeout: 5000 });
            await new Promise((resolve) => setTimeout(resolve, 500));
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 500 * scale, height: 200 * scale, left: 0, top: 0 });
        },
    },
    'panels/styling/sidebar-width': {
        url: 'admin?sidebarWidth=24rem',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/configuration/simple-page-max-content-width': {
        url: 'admin/login?no_auto_login=1&simplePageMaxContentWidth=sm',
        selector: 'body',
        viewport: {
            width: 800,
            height: 600,
            deviceScaleFactor: 3,
        },
        needsReloadForDarkMode: true,
        before: async (page) => {
            await page.$eval('input[type="email"]', (el) => el.blur());
            await new Promise((resolve) => setTimeout(resolve, 300));
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 800 * scale, height: 580 * scale, left: 0, top: 0 });
        },
    },
    'panels/resources/listing': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 2000))
            await page.evaluate(() => {
                document.querySelectorAll('.fi-wi-stats-overview, .fi-sc-tabs').forEach(el => el.remove())
            })

            await new Promise((resolve) => setTimeout(resolve, 200))
        },
    },
    'panels/resources/listing-tabs': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 2000))
            await page.evaluate(() => {
                document.querySelectorAll('.fi-wi-stats-overview').forEach(el => el.remove())
            })

            await new Promise((resolve) => setTimeout(resolve, 200))
        },
    },
    'panels/resources/listing-tabs-icons': {
        url: 'admin/posts?tabStyle=icons',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 1200 * scale, height: 300 * scale, left: 240 * scale, top: 50 * scale })
        },
    },
    'panels/resources/listing-tabs-badge-colors': {
        url: 'admin/posts?tabStyle=badgeColors',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 2000))
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 1200 * scale, height: 300 * scale, left: 240 * scale, top: 50 * scale })
        },
    },
    'panels/resources/creating': {
        url: 'admin/posts/create',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/creating-wizard': {
        url: 'admin/posts/create-wizard',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/simple-modal-create': {
        url: 'admin/tags',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-header-actions-ctn button')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
            await page.$eval('.fi-modal-window-ctn input', (el) => el.blur())
        },
    },
    'panels/resources/editing': {
        url: 'admin/posts/1/edit',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/editing-combined-tabs': {
        url: 'admin/posts/1/edit?combinedTabs=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/viewing': {
        url: 'admin/posts/1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/relation-manager': {
        url: 'admin/users/1/edit?noSubNav=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/sub-navigation': {
        url: 'admin/users/1/edit?noPostsSubNav=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/sub-navigation-end': {
        url: 'admin/users/1/edit?subNavPosition=end&noPostsSubNav=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/sub-navigation-top': {
        url: 'admin/users/1/edit?subNavPosition=top&noPostsSubNav=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/custom-page': {
        url: 'admin/analytics',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/trashed': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            // Click the filter icon to open filters panel
            await page.click('.fi-ta-header-ctn .fi-icon-btn:last-child')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/cluster': {
        url: 'admin/settings/manage-general',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/cluster-end': {
        url: 'admin/settings/manage-general?subNavPosition=end',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/cluster-top': {
        url: 'admin/settings/manage-general?subNavPosition=top',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/widgets': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 1000,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/global-search': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 640,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-global-search-field input')
            await page.type('.fi-global-search-field input', 'Filament')
            await new Promise((resolve) => setTimeout(resolve, 1500))
        },
        crop: (image) => {
            return image.extract({ width: 840 * 3, height: 440 * 3, left: 600 * 3, top: 0 })
        },
    },
    'panels/resources/global-search-details': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 700,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-global-search-field input')
            await page.type('.fi-global-search-field input', 'Filament')
            await new Promise((resolve) => setTimeout(resolve, 1500))
        },
        crop: (image) => {
            return image.extract({ width: 740 * 3, height: 440 * 3, left: 630 * 3, top: 0 })
        },
    },
    'panels/resources/global-search-actions': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 700,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('.fi-global-search-field input')
            await page.type('.fi-global-search-field input', 'Filament')
            await new Promise((resolve) => setTimeout(resolve, 1500))
        },
        crop: (image) => {
            return image.extract({ width: 740 * 3, height: 500 * 3, left: 630 * 3, top: 0 })
        },
    },
    'panels/resources/global-search-key-binding': {
        url: 'admin/posts',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 1200 * scale, height: 200 * scale, left: 240 * scale, top: 0 })
        },
    },
    'actions/import-action/modal': {
        url: 'actions-crud',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 640,
            height: 500,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('[wire\\:click*="mountAction(\'import\'"]')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'actions/export-action/modal': {
        url: 'actions-crud',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 800,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('[wire\\:click*="mountAction(\'export\'"]')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        crop: (image) => {
            const scale = 3
            return image.extract({ width: 1080 * scale, height: 780 * scale, left: 0, top: 0 })
        },
    },
    'panels/resources/nested': {
        url: 'admin/posts/1/comment-resource/comments',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/editing-section-actions': {
        url: 'admin/posts/1/edit-section-actions',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/editing-header-actions': {
        url: 'admin/posts/1/edit-header-actions',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/editing-save-in-header': {
        url: 'admin/posts/1/edit-save-in-header',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/creating-header-action': {
        url: 'admin/posts/create-header-action',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'panels/resources/relation-manager-attach': {
        url: 'admin/posts/1/edit',
        selector: '.fi-modal-window-ctn',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 1000))
            // Click the "Tags" button to switch to tags relation manager
            const buttons = await page.$$('button')
            for (const btn of buttons) {
                const text = await btn.evaluate((el) => el.textContent.trim())
                if (text === 'Tags') {
                    await btn.scrollIntoView()
                    await btn.click()
                    break
                }
            }
            await new Promise((resolve) => setTimeout(resolve, 1500))
            // Click the "Attach" header action button
            const buttons2 = await page.$$('button')
            for (const btn of buttons2) {
                const text = await btn.evaluate((el) => el.textContent.trim())
                if (text === 'Attach') {
                    await btn.scrollIntoView()
                    await btn.click()
                    break
                }
            }
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/resources/relation-manager-grouped': {
        url: 'admin/posts/1/edit?groupedRelations=1',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await new Promise((resolve) => setTimeout(resolve, 2000))

            // Scroll to the relation managers section so the tabs are visible
            await page.evaluate(() => {
                const relManagerSection = document.querySelector('.fi-resource-relation-managers')
                if (relManagerSection) {
                    relManagerSection.scrollIntoView({ block: 'start' })
                    window.scrollBy(0, -60)
                }
            })
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/button/simple': {
        url: 'components',
        selector: '#buttonSimple',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/button/sizes': {
        url: 'components',
        selector: '#buttonSizes',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/button/colors': {
        url: 'components',
        selector: '#buttonColors',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/button/outlined': {
        url: 'components',
        selector: '#buttonOutlined',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/button/icon': {
        url: 'components',
        selector: '#buttonIcon',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/icon-button/simple': {
        url: 'components',
        selector: '#iconButton',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/badge/simple': {
        url: 'components',
        selector: '#badgeSimple',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/badge/colors': {
        url: 'components',
        selector: '#badgeColors',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/badge/icon': {
        url: 'components',
        selector: '#badgeIcon',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/link/simple': {
        url: 'components',
        selector: '#linkSimple',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/link/colors': {
        url: 'components',
        selector: '#linkColors',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/link/icon': {
        url: 'components',
        selector: '#linkIcon',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/section/simple': {
        url: 'components',
        selector: '#sectionSimple',
        viewport: { width: 768, height: 400, deviceScaleFactor: 3 },
    },
    'components/section/description': {
        url: 'components',
        selector: '#sectionDescription',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/tabs/simple': {
        url: 'components',
        selector: '#tabsSimple',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/tabs/badge': {
        url: 'components',
        selector: '#tabsBadge',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/input/simple': {
        url: 'components',
        selector: '#inputSimple',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/input/prefix': {
        url: 'components',
        selector: '#inputPrefix',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/input/icon': {
        url: 'components',
        selector: '#inputIcon',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/fieldset/simple': {
        url: 'components',
        selector: '#fieldsetSimple',
        viewport: { width: 768, height: 400, deviceScaleFactor: 3 },
    },
    'components/loading-indicator/simple': {
        url: 'components',
        selector: '#loadingIndicator',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/dropdown/simple': {
        url: 'components',
        selector: '#dropdownSimple',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.click('#dropdownSimple button')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/breadcrumbs/simple': {
        url: 'components',
        selector: '#breadcrumbsSimple',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/select/simple': {
        url: 'components',
        selector: '#selectSimple',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/checkbox/simple': {
        url: 'components',
        selector: '#checkboxSimple',
        viewport: { width: 576, height: 250, deviceScaleFactor: 3 },
    },
    'components/callout/simple': {
        url: 'components',
        selector: '#calloutSimple',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/callout/colors': {
        url: 'components',
        selector: '#calloutColors',
        viewport: { width: 768, height: 600, deviceScaleFactor: 3 },
    },
    'components/callout/footer': {
        url: 'components',
        selector: '#calloutFooter',
        viewport: { width: 768, height: 350, deviceScaleFactor: 3 },
    },
    'components/callout/controls': {
        url: 'components',
        selector: '#calloutControls',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/callout/no-icon': {
        url: 'components',
        selector: '#calloutNoIcon',
        viewport: { width: 768, height: 250, deviceScaleFactor: 3 },
    },
    'components/callout/heading-only': {
        url: 'components',
        selector: '#calloutHeadingOnly',
        viewport: { width: 768, height: 250, deviceScaleFactor: 3 },
    },
    'components/callout/custom-icon': {
        url: 'components',
        selector: '#calloutCustomIconBlade',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/callout/icon-color': {
        url: 'components',
        selector: '#calloutIconColor',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/callout/icon-sizes': {
        url: 'components',
        selector: '#calloutIconSizes',
        viewport: { width: 768, height: 450, deviceScaleFactor: 3 },
    },
    'components/callout/primary-color': {
        url: 'components',
        selector: '#calloutPrimaryColor',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/input/disabled': {
        url: 'components',
        selector: '#inputDisabled',
        viewport: { width: 576, height: 150, deviceScaleFactor: 3 },
    },
    'components/input/suffix-icon-color': {
        url: 'components',
        selector: '#inputSuffixIconColor',
        viewport: { width: 576, height: 150, deviceScaleFactor: 3 },
    },
    'components/badge/sizes': {
        url: 'components',
        selector: '#badgeSizes',
        viewport: { width: 576, height: 150, deviceScaleFactor: 3 },
    },
    'components/empty-state/simple': {
        url: 'components',
        selector: '#emptyStateSimple',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/modal/simple': {
        url: 'components',
        selector: '.fi-modal-window',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.click('#modalSimple button')
            await page.waitForSelector('.fi-modal-window')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/avatar/simple': {
        url: 'components',
        selector: '#avatarSimple',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/avatar/square': {
        url: 'components',
        selector: '#avatarSquare',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/avatar/sizes': {
        url: 'components',
        selector: '#avatarSizes',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/section/collapsible': {
        url: 'components',
        selector: '#sectionCollapsible',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/section/collapsed': {
        url: 'components',
        selector: '#sectionCollapsed',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/section/aside': {
        url: 'components',
        selector: '#sectionAside',
        viewport: { width: 1024, height: 500, deviceScaleFactor: 3 },
    },
    'components/tabs/vertical': {
        url: 'components',
        selector: '#tabsVertical',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/icon-button/sizes': {
        url: 'components',
        selector: '#iconButtonSizes',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/icon-button/colors': {
        url: 'components',
        selector: '#iconButtonColors',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/icon-button/badge': {
        url: 'components',
        selector: '#iconButtonBadge',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/button/badge': {
        url: 'components',
        selector: '#buttonBadge',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/link/badge': {
        url: 'components',
        selector: '#linkBadge',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/link/weights': {
        url: 'components',
        selector: '#linkWeights',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/dropdown/image': {
        url: 'components',
        selector: '#dropdownImage',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.click('#dropdownImage button')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/dropdown/width': {
        url: 'components',
        selector: '#dropdownWidth',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.click('#dropdownWidth button')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/dropdown/max-height': {
        url: 'components',
        selector: '#dropdownMaxHeight',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.click('#dropdownMaxHeight button')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/tabs/icon-position-after': {
        url: 'components',
        selector: '#tabsIconPositionAfter',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/tabs/icon': {
        url: 'components',
        selector: '#tabsIcon',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/modal/heading': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-heading"] .fi-modal-window',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-heading' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/link/sizes': {
        url: 'components',
        selector: '#linkSizes',
        viewport: { width: 576, height: 200, deviceScaleFactor: 3 },
    },
    'components/section/icon': {
        url: 'components',
        selector: '#sectionIcon',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/section/icon-color': {
        url: 'components',
        selector: '#sectionIconColor',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/section/icon-sizes': {
        url: 'components',
        selector: '#sectionIconSizes',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/section/after-header': {
        url: 'components',
        selector: '#sectionAfterHeader',
        viewport: { width: 768, height: 300, deviceScaleFactor: 3 },
    },
    'components/section/content-before': {
        url: 'components',
        selector: '#sectionContentBefore',
        viewport: { width: 1024, height: 500, deviceScaleFactor: 3 },
    },
    'components/modal/icon': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-icon"] .fi-modal-window',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-icon' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/dropdown/icons': {
        url: 'components',
        selector: '#dropdownIcons',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            const trigger = await page.$('#dropdownIcons button')
            await trigger.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 200))
            await trigger.click()
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/dropdown/badge': {
        url: 'components',
        selector: '#dropdownBadge',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            const trigger = await page.$('#dropdownBadge button')
            await trigger.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 200))
            await trigger.click()
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/dropdown/icon-colors': {
        url: 'components',
        selector: '#dropdownIconColors',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            const trigger = await page.$('#dropdownIconColors button')
            await trigger.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 200))
            await trigger.click()
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'components/modal/footer': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-footer"] .fi-modal-window',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-footer' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/modal/alignment': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-alignment"] .fi-modal-window',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-alignment' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/modal/width': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-width"] .fi-modal-window',
        viewport: { width: 1920, height: 640, deviceScaleFactor: 2 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-width' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/modal/sticky-header': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-sticky-header"] .fi-modal-window',
        viewport: { width: 768, height: 600, deviceScaleFactor: 2 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-sticky-header' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
            // Scroll modal content down to show sticky header
            const modalContent = await page.$('[data-fi-modal-id="demo-modal-sticky-header"] .fi-modal-content')
            if (modalContent) {
                await modalContent.evaluate((el) => el.scrollTop = 150)
            }
            await new Promise((resolve) => setTimeout(resolve, 300))
        },
    },
    'components/modal/sticky-footer': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-sticky-footer"] .fi-modal-window',
        viewport: { width: 768, height: 600, deviceScaleFactor: 2 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-sticky-footer' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/modal/slide-over': {
        url: 'components',
        selector: '[data-fi-modal-id="demo-modal-slide-over"] .fi-modal-window',
        viewport: { width: 1200, height: 600, deviceScaleFactor: 2 },
        before: async (page) => {
            await page.evaluate(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'demo-modal-slide-over' } }))
            })
            await new Promise((resolve) => setTimeout(resolve, 1000))
        },
    },
    'components/empty-state/description': {
        url: 'components',
        selector: '#emptyStateDescription',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/empty-state/icon-color': {
        url: 'components',
        selector: '#emptyStateIconColor',
        viewport: { width: 768, height: 400, deviceScaleFactor: 3 },
    },
    'components/empty-state/icon-sizes': {
        url: 'components',
        selector: '#emptyStateIconSizes',
        viewport: { width: 768, height: 700, deviceScaleFactor: 3 },
    },
    'components/empty-state/not-contained': {
        url: 'components',
        selector: '#emptyStateNotContained',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/empty-state/actions': {
        url: 'components',
        selector: '#emptyStateActions',
        viewport: { width: 768, height: 500, deviceScaleFactor: 3 },
    },
    'components/button/outlined-colors': {
        url: 'components',
        selector: '#buttonOutlinedColors',
        viewport: { width: 768, height: 200, deviceScaleFactor: 3 },
    },
    'components/dropdown/colors': {
        url: 'components',
        selector: '#dropdownColors',
        viewport: { width: 576, height: 500, deviceScaleFactor: 3 },
        before: async (page) => {
            const trigger = await page.$('#dropdownColors button')
            await trigger.scrollIntoView()
            await new Promise((resolve) => setTimeout(resolve, 200))
            await trigger.click()
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/tenancy': {
        url: 'tenancy/acme-inc',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.waitForSelector('.fi-tenant-menu-trigger')
            await page.click('.fi-tenant-menu-trigger')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
        crop: (image) => {
            const scale = 3;
            return image.extract({ width: 600 * scale, height: 250 * scale, left: 0, top: 0 })
        },
    },
    'panels/tenancy/registration': {
        url: 'tenancy/new',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 600,
            deviceScaleFactor: 3,
        },
    },
    'panels/tenancy/profile': {
        url: 'tenancy/acme-inc/profile',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
    },
    'components/pagination/simple': {
        url: 'pagination',
        selector: '#paginationSimple',
        viewport: {
            width: 1024,
            height: 200,
            deviceScaleFactor: 3,
        },
    },
    'components/pagination/simple-paginator': {
        url: 'pagination',
        selector: '#paginationSimplePaginator',
        viewport: {
            width: 576,
            height: 200,
            deviceScaleFactor: 3,
        },
    },
    'components/pagination/extreme-links': {
        url: 'pagination',
        selector: '#paginationExtremeLinks',
        viewport: {
            width: 1024,
            height: 200,
            deviceScaleFactor: 3,
        },
    },
    'components/pagination/page-options': {
        url: 'pagination',
        selector: '#paginationPageOptions',
        viewport: {
            width: 1024,
            height: 200,
            deviceScaleFactor: 3,
        },
    },
    'tables/pagination/default': {
        url: 'tables?table=tablePaginationDefault',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/pagination/extreme': {
        url: 'tables?table=tablePaginationExtreme',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/pagination/cursor': {
        url: 'tables?table=tablePaginationCursor',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'tables/pagination/simple': {
        url: 'tables?table=tablePaginationSimple',
        selector: 'body',
        viewport: {
            width: 1080,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/code-editor/wrap': {
        url: 'forms/fields',
        selector: '#codeEditorWrap',
        viewport: {
            width: 1920,
            height: 900,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/textarea/autosize': {
        url: 'forms/fields',
        selector: '#textareaAutosize',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'forms/fields/key-value/custom-labels': {
        url: 'forms/fields',
        selector: '#keyValueCustomLabels',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/custom-filters': {
        url: 'widgets',
        selector: '#chartCustomFilters',
        viewport: {
            width: 1920,
            height: 900,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            const el = await page.$('#chartCustomFilters .fi-wi-chart-filter .fi-dropdown-trigger button')
            if (el) {
                await el.scrollIntoView()
                await el.click()
                await new Promise((resolve) => setTimeout(resolve, 500))
            }
        },
    },
    'widgets/chart/collapsible': {
        url: 'widgets',
        selector: '#chartCollapsible',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'widgets/chart/max-height': {
        url: 'widgets',
        selector: '#chartMaxHeight',
        viewport: {
            width: 1920,
            height: 640,
            deviceScaleFactor: 3,
        },
    },
    'panels/dashboard-filter-action': {
        url: 'admin/dashboard-filter-action',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('[wire\\:click*="mountAction(\'filter\'"]')
            await page.waitForSelector('.fi-modal-window-ctn')
            await new Promise((resolve) => setTimeout(resolve, 500))
        },
    },
    'panels/dashboard-column-spans': {
        url: 'admin/dashboard-column-spans',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 960,
            deviceScaleFactor: 3,
        },
    },
    'panels/custom-page-subheading': {
        url: 'admin/analytics-subheading',
        selector: 'body',
        viewport: {
            width: 1440,
            height: 820,
            deviceScaleFactor: 3,
        },
        crop: (image) => {
            const scale = 3
            return image.extract({ width: 1200 * scale, height: 200 * scale, left: 240 * scale, top: 30 * scale })
        },
    },
}
