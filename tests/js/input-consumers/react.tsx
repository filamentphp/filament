import { createRef } from 'react'
import Input from '../../../packages/support/resources/js/react/Input'
export const input = (
    <Input
        ref={createRef<HTMLInputElement>()}
        type="number"
        defaultValue={0}
        inlinePrefix
        readOnly
        onInput={(event) => event.currentTarget.checkValidity()}
    />
)
// @ts-expect-error Inline flags are boolean.
export const invalid = <Input inlineSuffix="yes" />
// @ts-expect-error Inputs are void elements.
export const children = <Input>Text</Input>
