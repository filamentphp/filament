import { createRef } from 'react'
import Radio from '../../../packages/support/resources/js/react/Radio'

export const controlled = (
    <Radio
        checked={false}
        onChange={(event) => event.currentTarget.value}
        ref={createRef<HTMLInputElement>()}
        valid={false}
        name="delivery"
        value="express"
        required
        form="order"
    />
)
export const uncontrolled = <Radio defaultChecked disabled />
// @ts-expect-error The primitive is always a radio.
export const invalidType = <Radio type="checkbox" />
// @ts-expect-error Inputs cannot have children.
export const invalidChildren = <Radio>Content</Radio>
// @ts-expect-error Raw markup is unsupported.
export const invalidHtml = <Radio dangerouslySetInnerHTML={{ __html: '' }} />
