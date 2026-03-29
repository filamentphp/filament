// Usage
//   node script.js
//   node script.js "absolute/schema/key"
//   node script.js "wildcard/schema/key/*"
//   node script.js --clean          # Delete screenshot files with no schema.js entry
//   node script.js --clean --dry    # Preview what --clean would delete
//
// For Apple Silicon, you might need to export the following variables if Chromium cannot be found:
// export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
// export PUPPETEER_EXECUTABLE_PATH=`which chromium`

import fs from 'fs'
import path from 'path'
import puppeteer from 'puppeteer'
import schema from './schema.js'
import emitter from 'events'
import * as process from 'process'
import sharp from 'sharp'

emitter.setMaxListeners(1024)

const themes = ['light', 'dark']

if (process.argv.includes('--clean')) {
    const dryRun = process.argv.includes('--dry')
    const schemaKeys = new Set(Object.keys(schema))
    let deletedCount = 0

    for (const theme of themes) {
        const imagesDir = `images/${theme}`

        if (! fs.existsSync(imagesDir)) {
            continue
        }

        const walkDirectory = (directory) => {
            for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
                const fullPath = path.join(directory, entry.name)

                if (entry.isDirectory()) {
                    walkDirectory(fullPath)
                    continue
                }

                if (! entry.name.endsWith('.jpg')) {
                    continue
                }

                const schemaKey = fullPath
                    .replace(`${imagesDir}/`, '')
                    .replace(/\.jpg$/, '')

                if (! schemaKeys.has(schemaKey)) {
                    if (dryRun) {
                        console.log(`🗑️  Would delete ${fullPath}`)
                    } else {
                        fs.unlinkSync(fullPath)
                        console.log(`🗑️  Deleted ${fullPath}`)
                    }

                    deletedCount++
                }
            }
        }

        walkDirectory(imagesDir)
    }

    if (deletedCount === 0) {
        console.log('✅  No orphaned screenshots found.')
    } else if (dryRun) {
        console.log(`\n${deletedCount} file(s) would be deleted. Run without --dry to delete.`)
    } else {
        console.log(`\n🗑️  Deleted ${deletedCount} orphaned screenshot(s).`)
    }

    process.exit(0)
}

const processScreenshot = async (file, options, theme) => {
    configure(options.configure)

    const directory = file.substring(0, file.lastIndexOf('/'))

    if (directory) {
        fs.mkdirSync(`images/${theme}/${directory}`, { recursive: true })
    }

    const browser = await puppeteer.launch()
    const page = await browser.newPage()
    await page.setViewport(
        options.viewport ?? {
            width: 1920,
            height: 1080,
            deviceScaleFactor: 3,
        },
    )

    // Set color scheme preference before navigating so server-rendered
    // pages (like auth pages) pick up the correct theme on first load.
    await page.emulateMediaFeatures([
        {
            name: 'prefers-color-scheme',
            value: theme === 'dark' ? 'dark' : 'light',
        },
    ])

    await page.goto(`http://127.0.0.1:8000/${options.url}`, {
        waitUntil: 'networkidle2',
    })

    if (theme === 'dark') {
        if (options.needsReloadForDarkMode) {
            await page.goto(`http://127.0.0.1:8000/${options.url}`, {
                waitUntil: 'networkidle2',
            })
        }

        await new Promise((resolve) => setTimeout(resolve, 500))
    } else {
        await new Promise((resolve) => setTimeout(resolve, 500))
    }

    // Scroll element into view so that lazy-loaded / JS-initialised content
    // renders correctly.  Skip the automatic scroll when a `before` callback
    // is present — those callbacks manage their own scrolling and a global
    // scroll beforehand can break them (e.g. modals that click buttons,
    // dropdowns that rely on page position).
    if (! options.before) {
        const preElement = await page.waitForSelector(options.selector)
        await preElement.scrollIntoView()
        await preElement.dispose()
        await new Promise((resolve) => setTimeout(resolve, 500))
    }

    if (options.before) {
        await options.before(page, browser)
    }

    const element = await page.waitForSelector(options.selector)

    if (options.selectorPadding) {
        const boundingBox = await element.boundingBox()
        const raw = options.selectorPadding
        const padding = typeof raw === 'number'
            ? { top: raw, right: raw, bottom: raw, left: raw }
            : raw
        const rawX = boundingBox.x - (padding.left ?? 0)
        const rawY = boundingBox.y - (padding.top ?? 0)
        const clippedX = Math.max(0, rawX)
        const clippedY = Math.max(0, rawY)
        await page.screenshot({
            path: `images/${theme}/${file}.jpg`,
            clip: {
                x: clippedX,
                y: clippedY,
                width: boundingBox.width + (padding.left ?? 0) + (padding.right ?? 0) - (clippedX - rawX),
                height: boundingBox.height + (padding.top ?? 0) + (padding.bottom ?? 0) - (clippedY - rawY),
            },
        })
    } else if (options.selector === 'body') {
        await page.screenshot({ path: `images/${theme}/${file}.jpg` })
    } else {
        await element.screenshot({ path: `images/${theme}/${file}.jpg` })
    }

    await element.dispose()
    await browser.close()

    configure()

    if (options.crop) {
        fs.createWriteStream(`images/${theme}/${file}.jpg`).write(
            await options.crop(sharp(fs.readFileSync(`images/${theme}/${file}.jpg`))).toBuffer()
        )
    }
}

const failures = []

const stringMatchesRule = (string, rule) => {
    const escapeRegex = (str) => str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1')

    return new RegExp('^' + rule.split('*').map(escapeRegex).join('.*') + '$').test(string)
}

for (const theme of themes) {
    for (const [file, options] of Object.entries(schema)) {
        const filters = process.argv.slice(2)

        if (filters.length && ! filters.some((filter) => stringMatchesRule(file, filter))) {
            continue
        }

        console.log(`⏳  Processing ${theme}/${file}`)

        try {
            await processScreenshot(file, options, theme)
        } catch (error) {
            console.error(`❌  Failed to generate ${theme}/${file} - ${error}`)
            failures.push(`${theme}/${file}`)
        }
    }
}

if (failures.length) {
    console.error(`❌  Failed to generate ${failures.length} screenshots:`)
    failures.forEach((failure) => console.error(`-  ${failure}`))
    process.exit(1)
}

process.exit(0)

function configure(php = null) {
    fs.writeFileSync(
        '../app/app/Providers/Filament/configure.php',
        `<?php

use Filament\\Panel;

return function (Panel $panel): void {
    ${php ?? '//'}
};
`,
    )
}
