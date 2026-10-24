import { execFileSync } from 'node:child_process'
import { buildViteRenderers } from './build-vite-renderers.js'

await buildViteRenderers(
    { avatar: 'tests/js/renderers/avatar.svelte.js' },
    'build/js-avatar-renderers',
)

for (const [command, arguments_] of [
    [
        'node_modules/typescript/bin/tsc',
        ['--project', 'tests/js/avatar.tsconfig.json'],
    ],
    [
        'node_modules/vue-tsc/bin/vue-tsc.js',
        ['--project', 'tests/js/avatar.tsconfig.json'],
    ],
    [
        'node_modules/svelte-check/bin/svelte-check',
        ['--tsconfig', 'tests/js/avatar.tsconfig.json', '--fail-on-warnings'],
    ],
]) {
    execFileSync(process.execPath, [command, ...arguments_], {
        stdio: 'inherit',
    })
}
