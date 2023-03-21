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
            height: 240,
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
            height: 240,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#confirmationModalCustomTextAction button')
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
    'app/dashboard': {
        url: 'admin',
        selector: 'body',
    },
}
