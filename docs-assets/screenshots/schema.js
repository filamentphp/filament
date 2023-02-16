export default {
    'app/dashboard': {
        url: 'admin',
        selector: 'body',
        configure: `
            $context->brandName('Test dashboard');
        `,
    },
}
