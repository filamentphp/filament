import fs from 'fs'
import puppeteer from 'puppeteer'
import schema from './schema.js'
import emitter from 'events'

fs.rmSync('images', { recursive: true, force: true })

emitter.setMaxListeners(1024)

const themes = ['light', 'dark']

themes.forEach((theme) => {
    for (const [file, options] of Object.entries(schema)) {
        configure(options.configure)

        const directory = file.substring(0, file.lastIndexOf('/'))

        if (directory) {
            fs.mkdirSync(`images/${theme}/${directory}`, { recursive: true })
        }

        ;(async () => {
            const browser = await puppeteer.launch({
                executablePath: '/opt/homebrew/bin/chromium',
            })
            const page = await browser.newPage()
            await page.setViewport(
                options.viewport ?? {
                    width: 1920,
                    height: 1080,
                    deviceScaleFactor: 3,
                },
            )
            await page.goto(`http://127.0.0.1:8000/${options.url}`, {
                waitUntil: 'networkidle2',
            })

            if (theme === 'dark') {
                await page.emulateMediaFeatures([
                    {
                        name: 'prefers-color-scheme',
                        value: 'dark',
                    },
                ])

                await new Promise((resolve) => setTimeout(resolve, 500))
            }

            if (options.before) {
                await options.before(page, browser)
            }

            const element = await page.waitForSelector(options.selector)
            await element.screenshot({ path: `images/${theme}/${file}.jpg` })

            await element.dispose()
            await browser.close()

            configure()
        })()
    }
})

function configure(php = null) {
    fs.writeFileSync(
        '../app/app/Providers/Filament/configure.php',
        `<?php

use Filament\\Context;

return function (Context $context) {
    ${php ?? '//'}
};
`,
    )
}
