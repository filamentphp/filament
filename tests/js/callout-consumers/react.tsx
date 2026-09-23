import { createRef } from 'react'
import Callout from '../../../packages/support/resources/js/react/Callout'
export const example = (
    <Callout
        ref={createRef<HTMLDivElement>()}
        color="brand"
        heading={<strong>Notice</strong>}
        description="Review"
        controls={<button>Help</button>}
        icon={<svg />}
        iconColor="gray"
        iconSize="sm"
        title="Notice"
        onClick={(event) => event.currentTarget.focus()}
    >
        <button>Continue</button>
    </Callout>
)
// @ts-expect-error `iconSize` must be a supported size.
export const invalid = <Callout iconSize="huge" />
