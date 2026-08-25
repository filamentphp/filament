import { createHash } from 'crypto'
import * as fs from 'fs'
import subsetFont from 'subset-font'

/**
 * Inter is subsetted from the official upstream release (`inter-ui`) rather
 * than the Google Fonts build, because Google strips the OpenType features we
 * want to expose: slashed zero (`zero`), the stylistic sets (`ss01`-`ss08`) and
 * the character variants (`cv01`-`cv14`). Tabular figures (`tnum`) survive in
 * both builds, but everything else does not.
 *
 * The upstream release ships as one file covering every script, so it is split
 * here into the same `unicode-range` subsets Google Fonts uses. The `opsz` axis
 * is pinned to its default so that rendering matches the previous build, and so
 * that the added feature coverage stays close to size-neutral.
 */
const INTER_SUBSETS = {
    'cyrillic-ext':
        'U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F',
    cyrillic: 'U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116',
    'greek-ext': 'U+1F00-1FFF',
    greek: 'U+0370-0377,U+037A-037F,U+0384-038A,U+038C,U+038E-03A1,U+03A3-03FF',
    vietnamese:
        'U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB',
    'latin-ext':
        'U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF',
    latin: 'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD',
}

const INTER_OPTICAL_SIZE = 14

const BASE_32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'

/**
 * Content hash in the same shape esbuild produced for the previous font files,
 * so that cached copies of the old files are never served for new content.
 */
function hashContent(content) {
    const digest = createHash('sha256').update(content).digest()

    let hash = ''

    for (let index = 0; index < 8; index++) {
        hash += BASE_32_ALPHABET[digest[index] % 32]
    }

    return hash
}

/**
 * `subset-font` keeps glyphs by character rather than by `unicode-range`, so
 * each range is expanded into the characters it covers.
 */
function expandUnicodeRange(unicodeRange) {
    let characters = ''

    for (const range of unicodeRange.split(',')) {
        const [start, end] = range
            .replace('U+', '')
            .split('-')
            .map((codePoint) => parseInt(codePoint, 16))

        for (let codePoint = start; codePoint <= (end ?? start); codePoint++) {
            characters += String.fromCodePoint(codePoint)
        }
    }

    return characters
}

export async function buildInter(directory) {
    const font = fs.readFileSync(
        './node_modules/inter-ui/variable/InterVariable.woff2',
    )

    let css = ''

    for (const [subset, unicodeRange] of Object.entries(INTER_SUBSETS)) {
        const subsetFontFile = await subsetFont(
            font,
            expandUnicodeRange(unicodeRange),
            {
                targetFormat: 'woff2',
                variationAxes: { opsz: INTER_OPTICAL_SIZE },
            },
        )

        const fileName = `inter-${subset}-wght-normal-${hashContent(subsetFontFile)}.woff2`

        fs.writeFileSync(`${directory}/${fileName}`, subsetFontFile)

        css +=
            `@font-face{font-family:Inter Variable;font-style:normal;font-display:swap;font-weight:100 900;` +
            `src:url("./${fileName}") format("woff2-variations");unicode-range:${unicodeRange}}`
    }

    fs.writeFileSync(`${directory}/index.css`, css)
}
