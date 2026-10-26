import assert from 'node:assert/strict'
import { test } from 'node:test'
import { build } from 'esbuild'

const result = await build({
    entryPoints: ['packages/support/resources/js/components/avatar.ts'],
    format: 'esm',
    write: false,
})
const { getAvatarClasses } = await import(
    `data:text/javascript;base64,${Buffer.from(result.outputFiles[0].text).toString('base64')}`
)

test('avatar defaults match Blade', () => {
    assert.equal(getAvatarClasses(), 'fi-avatar fi-circular fi-size-md')
})

test('avatar rounding and sizes are independent', () => {
    for (const size of ['sm', 'md', 'lg']) {
        assert.equal(getAvatarClasses(false, size), `fi-avatar fi-size-${size}`)
        assert.equal(
            getAvatarClasses(true, size),
            `fi-avatar fi-circular fi-size-${size}`,
        )
    }
    assert.equal(
        getAvatarClasses(false, 'profile-avatar'),
        'fi-avatar profile-avatar',
    )
    assert.equal(
        getAvatarClasses(true, 'w-12 h-12'),
        'fi-avatar fi-circular w-12 h-12',
    )
    assert.equal(getAvatarClasses(false, ''), 'fi-avatar')
})
