// Usage
//   node script.js
//   node script.js "absolute/schema/key"
//   node script.js "wildcard/schema/key/*"
//   node script.js --force          # Overwrite files even when the new screenshot is visually identical
//   node script.js --parallel       # Process screenshots in parallel, one server + database copy per worker
//   node script.js --parallel=8     # Use a specific number of parallel workers
//   node script.js --clean          # Delete screenshot files with no schema.js entry
//   node script.js --clean --dry    # Preview what --clean would delete
//
// Serial mode expects `php artisan serve` to be running on http://127.0.0.1:8000.
// Parallel mode starts its own servers on ports 8001+, each with its own copy of
// `database/database.sqlite`, because many demos mutate the database on mount and
// would corrupt each other's screenshots if they shared one database.
//
// For Apple Silicon, you might need to export the following variables if Chromium cannot be found:
// export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
// export PUPPETEER_EXECUTABLE_PATH=`which chromium`

import fs from 'fs'
import os from 'os'
import path from 'path'
import puppeteer from 'puppeteer'
import schema from './schema.js'
import emitter from 'events'
import process from 'process'
import sharp from 'sharp'
import pixelmatch from 'pixelmatch'
import { spawn } from 'child_process'

emitter.setMaxListeners(1024)

const themes = ['light', 'dark']

// When a screenshot is regenerated, tiny pixel-level differences (anti-aliasing,
// font rasterization, JPEG encoding) appear between runs and Chrome versions even
// though nothing visible changed. To avoid committing that noise, the existing
// file is kept unless at least this ratio of pixels is visually different.
// Pass --force to overwrite regardless.
const visualDifferenceRatioThreshold = 0.001

const isForced = process.argv.includes('--force')

const parallelArgument = process.argv.find(
    (argument) => argument === '--parallel' || argument.startsWith('--parallel='),
)

const workerCount = parallelArgument
    ? parallelArgument.includes('=')
        ? Math.max(1, parseInt(parallelArgument.split('=')[1], 10) || 1)
        : Math.max(1, Math.min(6, os.cpus().length - 2))
    : 1

const visualDifferenceRatio = async (previousImage, newImage) => {
    const [previous, current] = await Promise.all([
        sharp(previousImage).ensureAlpha().raw().toBuffer({ resolveWithObject: true }),
        sharp(newImage).ensureAlpha().raw().toBuffer({ resolveWithObject: true }),
    ])

    if (
        previous.info.width !== current.info.width ||
        previous.info.height !== current.info.height
    ) {
        return 1
    }

    const mismatchedPixels = pixelmatch(
        previous.data,
        current.data,
        null,
        previous.info.width,
        previous.info.height,
        { threshold: 0.1 },
    )

    return mismatchedPixels / (previous.info.width * previous.info.height)
}

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

// Launching a Chromium process takes ~800ms, so one browser is shared for the
// whole run and each job gets its own browser context instead, which is just as
// isolated (fresh cookies, storage, and cache) but takes ~100ms.
let sharedBrowser = null

const getBrowser = async () => (sharedBrowser ??= await puppeteer.launch())

const captureEntry = async (page, browser, file, options, theme) => {
    console.log(`⏳  Processing ${theme}/${file}`)

    const directory = file.substring(0, file.lastIndexOf('/'))

    if (directory) {
        fs.mkdirSync(`images/${theme}/${directory}`, { recursive: true })
    }

    const filePath = `images/${theme}/${file}.jpg`
    const previousImage = fs.existsSync(filePath) ? fs.readFileSync(filePath) : null

    // Scroll element into view so that lazy-loaded / JS-initialised content
    // renders correctly.  Skip the automatic scroll when a `before` callback
    // is present — those callbacks manage their own scrolling and a global
    // scroll beforehand can break them (e.g. modals that click buttons,
    // dropdowns that rely on page position).
    if (! options.before) {
        // Always scroll from the top of the page, so that an element lands at
        // the same sub-pixel offset regardless of which entry on a batched
        // page was captured before it. A different offset changes how text is
        // rasterized, which would count as a visual change against the
        // existing file.
        await page.evaluate(() => window.scrollTo(0, 0))

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
        const padding =
            typeof raw === 'number'
                ? { top: raw, right: raw, bottom: raw, left: raw }
                : raw
        const rawX = boundingBox.x - (padding.left ?? 0)
        const rawY = boundingBox.y - (padding.top ?? 0)
        const clippedX = Math.max(0, rawX)
        const clippedY = Math.max(0, rawY)
        await page.screenshot({
            path: filePath,
            clip: {
                x: clippedX,
                y: clippedY,
                width:
                    boundingBox.width +
                    (padding.left ?? 0) +
                    (padding.right ?? 0) -
                    (clippedX - rawX),
                height:
                    boundingBox.height +
                    (padding.top ?? 0) +
                    (padding.bottom ?? 0) -
                    (clippedY - rawY),
            },
        })
    } else if (options.selector === 'body') {
        await page.screenshot({ path: filePath })
    } else {
        await element.screenshot({ path: filePath })
    }

    await element.dispose()

    if (options.crop) {
        fs.writeFileSync(
            filePath,
            await options.crop(sharp(fs.readFileSync(filePath))).toBuffer(),
        )
    }

    if (previousImage && (! isForced)) {
        const differenceRatio = await visualDifferenceRatio(previousImage, fs.readFileSync(filePath))

        if (differenceRatio <= visualDifferenceRatioThreshold) {
            fs.writeFileSync(filePath, previousImage)
            console.log(`💤  ${theme}/${file} is visually unchanged (${(differenceRatio * 100).toFixed(4)}% of pixels), kept the existing file`)

            return
        }

        console.log(`✏️  ${theme}/${file} changed (${(differenceRatio * 100).toFixed(4)}% of pixels)`)
    }
}

// A job is one page load capturing one or more schema entries. Entries without
// page interactions (`before`, `configure`) that share the same URL, viewport,
// and theme are batched into a single job, because loading and settling a page
// costs far more than capturing an extra element from it.
const processJob = async (job, baseUrl = 'http://127.0.0.1:8000') => {
    const { theme, entries } = job
    const options = entries[0].options

    if (options.configure) {
        configure(options.configure)
    }

    let context = null

    try {
        const browser = await getBrowser()
        context = await browser.createBrowserContext()

        const page = await context.newPage()
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
            {
                name: 'prefers-reduced-motion',
                value: 'reduce',
            },
        ])

        // Freeze CSS animations, transitions, and the blinking input caret, so
        // that a screenshot never depends on which animation frame it happened
        // to capture (e.g. spinning loading indicators, modal fade-ins).
        await page.evaluateOnNewDocument(() => {
            document.addEventListener('DOMContentLoaded', () => {
                const style = document.createElement('style')
                style.textContent = `
                    *, ::before, ::after {
                        animation: none !important;
                        transition: none !important;
                        caret-color: transparent !important;
                    }

                    html {
                        scroll-behavior: auto !important;
                    }
                `
                document.head.append(style)
            })
        })

        await page.goto(`${baseUrl}/${options.url}`, {
            waitUntil: 'networkidle2',
        })

        if (theme === 'dark' && options.needsReloadForDarkMode) {
            await page.goto(`${baseUrl}/${options.url}`, {
                waitUntil: 'networkidle2',
            })
        }

        await new Promise((resolve) => setTimeout(resolve, 500))

        for (const { file, options: entryOptions } of entries) {
            try {
                await captureEntry(page, browser, file, entryOptions, theme)
            } catch (error) {
                console.error(`❌  Failed to generate ${theme}/${file} - ${error}`)
                failures.push(`${theme}/${file}`)
            }
        }
    } catch (error) {
        for (const { file } of entries) {
            console.error(`❌  Failed to generate ${theme}/${file} - ${error}`)
            failures.push(`${theme}/${file}`)
        }
    } finally {
        await context?.close()

        if (options.configure) {
            configure()
        }
    }
}

const failures = []

const stringMatchesRule = (string, rule) => {
    const escapeRegex = (str) => str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1')

    return new RegExp('^' + rule.split('*').map(escapeRegex).join('.*') + '$').test(string)
}

const filters = process.argv.slice(2).filter((argument) => ! argument.startsWith('--'))

const jobs = []
const batchedJobs = new Map()

for (const theme of themes) {
    for (const [file, options] of Object.entries(schema)) {
        if (filters.length && ! filters.some((filter) => stringMatchesRule(file, filter))) {
            continue
        }

        const entry = { file, options }

        // Entries with page interactions get their own page load.
        if (options.before || options.configure) {
            jobs.push({ theme, entries: [entry] })

            continue
        }

        const batchKey = [
            theme,
            options.url,
            JSON.stringify(options.viewport ?? null),
            options.needsReloadForDarkMode ? 'reload' : '',
        ].join('|')

        if (! batchedJobs.has(batchKey)) {
            const job = { theme, entries: [] }
            batchedJobs.set(batchKey, job)
            jobs.push(job)
        }

        batchedJobs.get(batchKey).entries.push(entry)
    }
}

// Reset `configure.php` in case a previous run crashed while it held a value.
configure()

if (workerCount <= 1) {
    for (const job of jobs) {
        await processJob(job)
    }
} else {
    const appDirectory = path.resolve('../app')
    const sourceDatabasePath = path.join(appDirectory, 'database', 'database.sqlite')

    if (! fs.existsSync(sourceDatabasePath)) {
        console.error('❌  database/database.sqlite does not exist. Run `php artisan migrate:fresh --seed` in docs-assets/app first.')
        process.exit(1)
    }

    // Screenshots that write `configure.php` mutate state shared by every
    // server, so they run serially after the parallel pool has finished.
    const parallelJobs = jobs.filter(({ entries }) => ! entries[0].options.configure)
    const configureJobs = jobs.filter(({ entries }) => entries[0].options.configure)

    const servers = []

    const stopServers = () => {
        for (const server of servers) {
            try {
                process.kill(-server.pid, 'SIGTERM')
            } catch {
                //
            }

            try {
                fs.unlinkSync(server.databasePath)
            } catch {
                //
            }
        }
    }

    process.on('exit', stopServers)
    process.on('SIGINT', () => process.exit(130))
    process.on('SIGTERM', () => process.exit(143))

    const startServer = async (workerIndex) => {
        const port = 8001 + workerIndex
        const databasePath = path.join(appDirectory, 'database', `parallel-worker-${workerIndex}.sqlite`)

        fs.copyFileSync(sourceDatabasePath, databasePath)

        const baseUrl = `http://127.0.0.1:${port}`

        // `--no-reload` is required for `PHP_CLI_SERVER_WORKERS` to take
        // effect; without it, `artisan serve` ignores the variable.
        const serverProcess = spawn('php', ['artisan', 'serve', '--port', `${port}`, '--no-reload'], {
            cwd: appDirectory,
            env: {
                ...process.env,
                DB_DATABASE: databasePath,
                // Absolute URLs the server generates (e.g. `/storage` file
                // URLs from the `public` disk) must point at this worker's own
                // port, not the default port 8000.
                APP_URL: baseUrl,
                // Serve a page's many static asset requests concurrently.
                PHP_CLI_SERVER_WORKERS: process.env.PHP_CLI_SERVER_WORKERS ?? '8',
            },
            stdio: 'ignore',
            detached: true,
        })

        servers.push({ pid: serverProcess.pid, databasePath })

        for (let attempt = 0; attempt < 60; attempt++) {
            try {
                const response = await fetch(`${baseUrl}/up`)

                if (response.status === 200) {
                    // Warm up the application with a real page request, so a
                    // cold first request does not eat into a screenshot's
                    // settle waits.
                    await fetch(`${baseUrl}/forms/overview`).catch(() => {})

                    return { baseUrl, databasePath }
                }
            } catch {
                //
            }

            await new Promise((resolve) => setTimeout(resolve, 500))
        }

        throw new Error(`Server on port ${port} did not become ready.`)
    }

    console.log(`🚀  Starting ${workerCount} servers for ${parallelJobs.length} page loads...`)

    const workers = await Promise.all(
        Array.from({ length: workerCount }, (item, workerIndex) => startServer(workerIndex)),
    )

    // Demos mutate the database when they render (truncating and rebuilding
    // tables), so a job's outcome would otherwise depend on which jobs ran
    // before it on the same worker. Restoring the pristine database before
    // every job keeps each screenshot deterministic regardless of scheduling.
    const resetWorkerDatabase = (worker) => fs.copyFileSync(sourceDatabasePath, worker.databasePath)

    let nextJobIndex = 0

    await Promise.all(
        workers.map(async (worker) => {
            while (nextJobIndex < parallelJobs.length) {
                const job = parallelJobs[nextJobIndex++]

                resetWorkerDatabase(worker)
                await processJob(job, worker.baseUrl)
            }
        }),
    )

    for (const job of configureJobs) {
        resetWorkerDatabase(workers[0])
        await processJob(job, workers[0].baseUrl)
    }

    stopServers()
}

if (sharedBrowser) {
    await sharedBrowser.close()
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
