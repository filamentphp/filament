import { createRef } from 'react'
import EmptyState from '../../../packages/support/resources/js/react/EmptyState'

export const example = (
    <EmptyState
        ref={createRef<HTMLDivElement>()}
        heading={<strong>Projects</strong>}
        description="Start here"
        headingTag="h3"
        compact
        contained={false}
        icon={<svg />}
        iconSize="lg"
        iconColor="success"
        title="Projects"
        onClick={(event) => event.currentTarget.focus()}
    >
        <button>Create</button>
    </EmptyState>
)
// @ts-expect-error `headingTag` must be a heading element.
export const invalidHeading = <EmptyState headingTag="script" />
// @ts-expect-error `contained` must be a boolean.
export const invalidContained = <EmptyState contained="false" />
