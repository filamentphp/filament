import { createRef } from 'react'
import Select from '../../../packages/support/resources/js/react/Select'
export const select = (
    <Select
        ref={createRef<HTMLSelectElement>()}
        multiple
        defaultValue={['drawing']}
        inlinePrefix
        onChange={(event) => event.currentTarget.selectedOptions}
    >
        <optgroup label="Art">
            <option value="drawing">Drawing</option>
        </optgroup>
    </Select>
)
// @ts-expect-error Inline flags are boolean.
export const invalid = <Select inlinePrefix="yes" />
