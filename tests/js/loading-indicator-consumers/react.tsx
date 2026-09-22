import { createRef } from 'react'
import LoadingIndicator from '../../../packages/support/resources/js/react/LoadingIndicator'
export const indicator = (
    <LoadingIndicator
        size="2xl"
        ref={createRef<SVGSVGElement>()}
        aria-hidden={false}
        role="img"
        aria-label="Loading"
        viewBox="0 0 32 32"
        onClick={(event) => event.currentTarget.getBBox()}
    />
)
// @ts-expect-error Sizes match `IconSize`.
export const invalidSize = <LoadingIndicator size="huge" />
// @ts-expect-error The component owns its paths.
export const invalidChildren = <LoadingIndicator>Loading</LoadingIndicator>
export const invalidHtml = (
    // @ts-expect-error Raw markup is unsupported.
    <LoadingIndicator dangerouslySetInnerHTML={{ __html: '' }} />
)
