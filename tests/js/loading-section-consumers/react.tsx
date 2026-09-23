import { createRef } from 'react'
import LoadingSection from '../../../packages/support/resources/js/react/LoadingSection'
export const example = (
    <LoadingSection
        ref={createRef<HTMLDivElement>()}
        columnSpan={{ default: 'full', lg: 2 }}
        columnStart={3}
        height="12rem"
        loadingLabel="Loading projects"
        title="Projects"
        onClick={(event) => event.currentTarget.focus()}
    />
)
// @ts-expect-error `height` is a CSS length, not a pixel count.
export const invalid = <LoadingSection height={12} />
