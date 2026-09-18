import assert from 'node:assert/strict'
import { mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs'
import { tmpdir } from 'node:os'
import { join } from 'node:path'
import test from 'node:test'

import {
    updateDocumentationFiles,
    updateDocumentationVersion,
} from '../../bin/update-docs-version.js'

test('updates documentation component versions', () => {
    const content = `
<AutoScreenshot name="example" version="4.x" />

<UtilityInjection
    set="example"
    version="3.x"
>
    Example
</UtilityInjection>

<Aside version="4.x">Example</Aside>
`

    assert.equal(
        updateDocumentationVersion(content, '5.x'),
        `
<AutoScreenshot name="example" version="5.x" />

<UtilityInjection
    set="example"
    version="5.x"
>
    Example
</UtilityInjection>

<Aside version="4.x">Example</Aside>
`,
    )
})

test('rejects invalid documentation versions', () => {
    assert.throws(
        () => updateDocumentationVersion('', 'feature-branch'),
        new Error('Invalid documentation version [feature-branch].'),
    )
})

test('updates only files with incorrect documentation versions', (context) => {
    const directory = mkdtempSync(join(tmpdir(), 'filament-docs-version-'))
    const outdatedFilePath = join(directory, 'outdated.md')
    const currentFilePath = join(directory, 'current.md')

    context.after(() => rmSync(directory, { recursive: true }))

    writeFileSync(
        outdatedFilePath,
        '<AutoScreenshot name="example" version="4.x" />\n',
    )
    writeFileSync(
        currentFilePath,
        '<UtilityInjection set="example" version="5.x">Example</UtilityInjection>\n',
    )

    assert.equal(
        updateDocumentationFiles([outdatedFilePath, currentFilePath], '5.x'),
        1,
    )
    assert.equal(
        readFileSync(outdatedFilePath, 'utf8'),
        '<AutoScreenshot name="example" version="5.x" />\n',
    )
    assert.equal(
        readFileSync(currentFilePath, 'utf8'),
        '<UtilityInjection set="example" version="5.x">Example</UtilityInjection>\n',
    )
})
