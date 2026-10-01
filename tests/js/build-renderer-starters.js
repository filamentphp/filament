import { execFileSync } from 'node:child_process'
import { mkdir, readFile, writeFile } from 'node:fs/promises'
import { resolve } from 'node:path'
import { buildViteRenderers } from './build-vite-renderers.js'

// Each kind maps to its package or an object with explicit stub names.
export async function buildRendererStarters(kinds) {
    const entriesByKind = {}
    const productionEntries = {}
    const typeChecks = []

    for (const [kind, definition] of Object.entries(kinds)) {
        const {
            packageName,
            stubName = kind[0].toUpperCase() + kind.slice(1),
            componentStubName = `${stubName}Component`,
        } = typeof definition === 'string'
            ? { packageName: definition }
            : definition
        const componentName = `Generated${stubName}`
        const entries = (entriesByKind[kind] = {})

        for (const framework of ['js', 'react', 'vue', 'svelte']) {
            for (const isTypeScript of [false, true]) {
                const name = framework + (isTypeScript ? '-ts' : '')
                const directory = `build/generated-${kind}-sources/${name}`
                await mkdir(directory, { recursive: true })
                const extension =
                    framework === 'react'
                        ? isTypeScript
                            ? 'tsx'
                            : 'jsx'
                        : (framework === 'svelte' ? 'svelte.' : '') +
                          (isTypeScript ? 'ts' : 'js')
                const prefix =
                    framework[0].toUpperCase() +
                    framework.slice(1) +
                    (isTypeScript ? 'TypeScript' : '')
                const files = {
                    [`${prefix}${stubName}Renderer`]: `${kind}.${extension}`,
                }
                if (framework === 'vue' || framework === 'svelte') {
                    files[`${prefix}${componentStubName}`] =
                        `${componentName}.${framework}`
                }
                for (const [stub, filename] of Object.entries(files)) {
                    const source = await readFile(
                        `packages/${packageName}/stubs/${stub}.stub`,
                        'utf8',
                    )
                    await writeFile(
                        `${directory}/${filename}`,
                        source.replaceAll('{{ componentName }}', componentName),
                    )
                }
                entries[`generated-${name}`] =
                    `${directory}/${kind}.${extension}`
                productionEntries[`${kind}-${name}`] =
                    entries[`generated-${name}`]

                if (!isTypeScript) continue
                await writeFile(
                    `${directory}/tsconfig.json`,
                    JSON.stringify({
                        compilerOptions: {
                            strict: true,
                            noEmit: true,
                            target: 'ES2020',
                            module: 'ESNext',
                            moduleResolution: 'Bundler',
                            jsx: 'react-jsx',
                            paths: {
                                [`@filament/${packageName}/js-${kind}`]: [
                                    resolve(
                                        `packages/${packageName}/resources/js/types/js-${kind}.d.ts`,
                                    ),
                                ],
                            },
                        },
                        include: ['./**/*'],
                    }),
                )
                typeChecks.push(
                    framework === 'svelte'
                        ? [
                              'node_modules/svelte-check/bin/svelte-check',
                              '--tsconfig',
                              `${directory}/tsconfig.json`,
                              '--fail-on-warnings',
                          ]
                        : [
                              framework === 'vue'
                                  ? 'node_modules/vue-tsc/bin/vue-tsc.js'
                                  : 'node_modules/typescript/bin/tsc',
                              '--project',
                              `${directory}/tsconfig.json`,
                          ],
                )
            }
        }
    }

    await buildViteRenderers(productionEntries)
    for (const command of typeChecks) {
        execFileSync(process.execPath, command, { stdio: 'inherit' })
    }

    return entriesByKind
}
