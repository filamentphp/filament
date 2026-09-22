import { createRef } from 'react'
import Fieldset from '../../../packages/support/resources/js/react/Fieldset'

export const fieldset = (
    <Fieldset
        ref={createRef<HTMLFieldSetElement>()}
        label={<strong>Address</strong>}
        required
        labelHidden
        contained={false}
        disabled
        name="address"
        form="profile"
        onClick={(event) => event.currentTarget.checkValidity()}
    >
        <input aria-label="Street" />
    </Fieldset>
)
// @ts-expect-error `contained` must be a boolean.
export const invalidContained = <Fieldset contained="false" />
// @ts-expect-error `disabled` must be a boolean.
export const invalidDisabled = <Fieldset disabled="false" />
