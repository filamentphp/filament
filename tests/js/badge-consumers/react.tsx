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
