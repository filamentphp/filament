import { createRef } from 'react'
import LoadingSection from '../../../packages/support/resources/js/react/LoadingSection'
export const example = (
    <LoadingSection
        ref={createRef<HTMLDivElement>()}
        columnSpan={{ default: 'full', lg: 2 }}
        columnStart={{ default: '1e1', lg: 3 }}
        style={{ height: '10rem', '--col-span-lg': 'span 3 / span 3' }}
        height="12rem"
        loadingLabel="Loading projects"
        title="Projects"
        onClick={(event) => event.currentTarget.focus()}
    />
)
// @ts-expect-error `height` is a CSS length, not a pixel count.
export const invalid = <LoadingSection height={12} />
// @ts-expect-error `columnStart` does not accept span keywords.
export const invalidStart = <LoadingSection columnStart="full" />
// @ts-expect-error Native style property names remain checked.
export const invalidStyle = <LoadingSection style={{ heigth: '10rem' }} />
