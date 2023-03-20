export default {
    'actions/trigger-button/button': {
        url: 'actions',
        selector: '#buttonAction',
    },
    'actions/trigger-button/link': {
        url: 'actions',
        selector: '#linkAction',
    },
    'actions/trigger-button/icon-button': {
        url: 'actions',
        selector: '#iconButtonAction',
    },
    'actions/trigger-button/danger': {
        url: 'actions',
        selector: '#dangerAction',
    },
    'actions/trigger-button/large': {
        url: 'actions',
        selector: '#largeAction',
    },
    'actions/trigger-button/icon': {
        url: 'actions',
        selector: '#iconAction',
    },
    'actions/trigger-button/icon-after': {
        url: 'actions',
        selector: '#iconAfterAction',
    },
    'actions/modal/confirmation': {
        url: 'actions',
        viewport: {
            width: 640,
            height: 480,
            deviceScaleFactor: 3,
        },
        before: async (page) => {
            await page.click('#confirmationModalAction button')
            await page.waitForSelector('#modal h2')

            await new Promise((resolve) => setTimeout(resolve, 300))
        },
        selector: 'body',
    },
    'app/dashboard': {
        url: 'admin',
        selector: 'body',
    },
}
