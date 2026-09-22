import { createRef } from 'react'
import Avatar from '../../../packages/support/resources/js/react/Avatar'

const image = createRef<HTMLImageElement>()

export const avatar = (
    <Avatar
        ref={image}
        src="/portrait.jpg"
        alt="Profile"
        loading="lazy"
        className="profile-avatar"
        circular={false}
        size="lg"
        onLoad={(event) => event.currentTarget.decode()}
    />
)

// @ts-expect-error `loading` only accepts native image loading values.
export const invalidLoading = <Avatar loading="invalid" />

// @ts-expect-error `ref` refers to the image, not a wrapper.
export const invalidRef = <Avatar ref={createRef<HTMLButtonElement>()} />
