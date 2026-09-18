import { execFileSync } from 'node:child_process'
import { readFileSync, writeFileSync } from 'node:fs'
import { pathToFileURL } from 'node:url'

const documentationVersionPattern =
    /(<(?:AutoScreenshot|UtilityInjection)\b[^>]*?\bversion=")\d+\.x(")/gs

function validateVersion(version) {
    if (!/^\d+\.x$/.test(version)) {
        throw new Error(`Invalid documentation version [${version}].`)
    }
}

export function updateDocumentationVersion(content, version) {
    validateVersion(version)

    return content.replace(documentationVersionPattern, `$1${version}$2`)
}

export function updateDocumentationFiles(filePaths, version) {
    let updatedFileCount = 0

    for (const filePath of filePaths) {
        const content = readFileSync(filePath, 'utf8')
        const updatedContent = updateDocumentationVersion(content, version)

        if (updatedContent === content) {
            continue
        }

        writeFileSync(filePath, updatedContent)
        updatedFileCount++
    }

    return updatedFileCount
}

function getDocumentationFilePaths() {
    return execFileSync(
        'git',
        ['ls-files', '-z', '--', 'docs/*.md', 'packages/*/docs/*.md'],
        { encoding: 'utf8' },
    )
        .split('\0')
        .filter(Boolean)
}

if (import.meta.url === pathToFileURL(process.argv[1]).href) {
    try {
        const version = process.argv[2]
        const updatedFileCount = updateDocumentationFiles(
            getDocumentationFilePaths(),
            version,
        )

        console.log(
            `Updated documentation versions in ${updatedFileCount} file(s).`,
        )
    } catch (error) {
        console.error(error.message)
        process.exitCode = 1
    }
}
