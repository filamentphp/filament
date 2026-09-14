import type {
    ExternalNavigationOptions,
    PageProps,
    SharedPageProps,
} from '@inertiajs/core'
import { createInertiaApp } from '@inertiajs/svelte'

type CreateInertiaAppOptions<SharedProps extends PageProps> = NonNullable<
    Parameters<typeof createInertiaApp<SharedProps>>[0]
>

export type CreateRendererOptions<SharedProps extends PageProps> = Omit<
    CreateInertiaAppOptions<SharedProps>,
    | 'externalNavigation'
    | 'id'
    | 'page'
    | 'render'
    | 'resolve'
    | 'setup'
    | 'withApp'
> & {
    resolve: NonNullable<CreateInertiaAppOptions<SharedProps>['resolve']>
}

export function createRenderer<
    SharedProps extends PageProps = PageProps & SharedPageProps,
>(
    options: CreateRendererOptions<SharedProps>,
): (
    element: HTMLElement,
    externalNavigation: ExternalNavigationOptions,
    ready: () => void,
) => Promise<() => Promise<void> | undefined>
