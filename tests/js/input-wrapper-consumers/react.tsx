import { createRef } from 'react'
import InputWrapper from '../../../packages/support/resources/js/react/InputWrapper'
import Icon from '../../../packages/support/resources/js/react/Icon'

export const wrapper = (
    <InputWrapper
        ref={createRef<HTMLDivElement>()}
        disabled
        valid={false}
        inlinePrefix
        prefix="£"
        suffix={<strong>GBP</strong>}
        prefixIcon={<Icon src="/currency.svg" />}
        suffixActions={<button type="button">Clear</button>}
        onClick={(event) => event.currentTarget.focus()}
    >
        <input required />
    </InputWrapper>
)
// @ts-expect-error Wrapper state is boolean.
export const invalid = <InputWrapper valid="false" />
// @ts-expect-error PHP aliases are host-owned.
export const alias = <InputWrapper prefixIconAlias="currency" />
