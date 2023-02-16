import fs from 'fs'
import puppeteer from 'puppeteer'
import schema from './schema.js'

fs.rmSync('images', { recursive: true, force: true })

for (const [file, options] of Object.entries(schema)) {
    configure(options.configure)

    const directory = file.substring(0, file.lastIndexOf('/'))

    if (directory) {
        fs.mkdirSync(`images/${directory}`, { recursive: true })
    }

    (async () => {
        const browser = await puppeteer.launch()
        const page = await browser.newPage()
        await page.setViewport(options.viewport ?? {
            width: 1920,
            height: 1080,
            deviceScaleFactor: 1,
        })
        await page.goto(`http://127.0.0.1:8000/${options.url}`, {
            waitUntil: 'networkidle2',
        })

        const element = await page.waitForSelector(options.selector)
        await element.screenshot({ path: `images/${file}.jpg` })

        await element.dispose()
        await browser.close()
    })()
}

configure()

function configure(php = null) {
    fs.writeFileSync('../app/Providers/Filament/configure.php', `
        <?php

        use Filament\\Context;

        return function (Context $context) {
            ${php ?? '//'}
        };
    `)
}
