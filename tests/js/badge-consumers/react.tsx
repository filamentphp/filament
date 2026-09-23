import { createRef } from 'react'
import Badge from '../../../packages/support/resources/js/react/Badge'
const element = createRef<HTMLElement>()
;<Badge
    tag="button"
    type="submit"
    form="filters"
    ref={element}
    tooltip="Save filters"
    keyBindings={['mod+shift+b']}
    loading={false}
    onClick={(event) => event.preventDefault()}
>
    Save
</Badge>
;<Badge
    color="brand"
    size="xs"
    icon={<svg />}
    iconPosition="after"
    iconSize="lg"
    onDelete={(event) => event.currentTarget.focus()}
    deleteLabel="Remove priority"
    deleteLoading
>
    Priority
</Badge>
// @ts-expect-error Unsupported tags cannot contain delete buttons.
;<Badge tag="div" />
// @ts-expect-error HTML tooltips are intentionally unsupported.
;<Badge tooltip={{ html: '<b>Unsafe</b>' }} />
// @ts-expect-error Delete buttons cannot be nested in links.
;<Badge tag="a" onDelete={() => {}} />
// @ts-expect-error Delete buttons cannot be nested in buttons.
;<Badge tag="button" onDelete={() => {}} />
;<Badge
    onClick={(event) => {
        // @ts-expect-error The root is an `HTMLElement`, not both a button and anchor.
        event.currentTarget.href
    }}
/>
