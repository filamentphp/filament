import { createRef } from 'react'
import Actions from '../../../packages/support/resources/js/react/Actions'
export const example = (
    <Actions
        ref={createRef<HTMLDivElement>()}
        alignment="custom-layout"
        fullWidth
        title="Actions"
        onClick={(event) => event.currentTarget.focus()}
    >
        <button type="submit">Save</button>
    </Actions>
)
// @ts-expect-error `fullWidth` must be boolean.
export const invalid = <Actions fullWidth="yes" />
