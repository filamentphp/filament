import { createRef } from 'react'
import Checkbox from '../../../packages/support/resources/js/react/Checkbox'

export const controlled = (
    <Checkbox
        checked={false}
        onChange={(event) => event.currentTarget.checked}
        ref={createRef<HTMLInputElement>()}
        valid={false}
        name="terms"
        value="accepted"
        required
        form="profile"
    />
)
export const uncontrolled = <Checkbox defaultChecked disabled />
// @ts-expect-error The primitive is always a checkbox.
export const invalidType = <Checkbox type="radio" />
// @ts-expect-error Inputs cannot have children.
export const invalidChildren = <Checkbox>Content</Checkbox>
// @ts-expect-error Raw markup is unsupported.
export const invalidHtml = <Checkbox dangerouslySetInnerHTML={{ __html: '' }} />
