import { createRef } from 'react'
import Breadcrumbs from '../../../packages/support/resources/js/react/Breadcrumbs'

export const breadcrumbs = (
    <Breadcrumbs
        ref={createRef<HTMLElement>()}
        aria-label="Location"
        separator="/"
        separatorRtl="\\"
        breadcrumbs={[
            {
                label: 'Home',
                href: '/',
                target: '_blank',
                onClick: (event) => event.currentTarget.focus(),
            },
            { label: 'Current' },
        ]}
    />
)

// @ts-expect-error `label` is required.
export const missingLabel = <Breadcrumbs breadcrumbs={[{ href: '/' }]} />
export const invalidHref = (
    // @ts-expect-error `href` must be a string.
    <Breadcrumbs breadcrumbs={[{ label: 'Home', href: 42 }]} />
)
