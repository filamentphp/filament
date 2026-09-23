import { createRef } from 'react'
import Icon from '../../../packages/support/resources/js/react/Icon'

export const icon = (
    <Icon
        size="2xl"
        role="img"
        aria-label="Saved"
        ref={createRef<HTMLSpanElement | HTMLImageElement>()}
        onClick={(event) => event.currentTarget.focus()}
    >
        <svg viewBox="0 0 24 24">
            <path d="M4 12h16" />
        </svg>
    </Icon>
)
export const image = (
    <Icon
        src="/icon.svg"
        srcSet="/icon.svg 1x"
        decoding="async"
        alt="Saved"
        loading="lazy"
    />
)
// @ts-expect-error Sizes match `IconSize`.
export const invalidSize = <Icon size="huge" />
// @ts-expect-error PHP aliases are host-owned.
export const invalidAlias = <Icon alias="save" />
// @ts-expect-error Raw markup is unsupported.
export const invalidHtml = <Icon dangerouslySetInnerHTML={{ __html: '' }} />
