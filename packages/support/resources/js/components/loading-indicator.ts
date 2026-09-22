export type LoadingIndicatorSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'

export const loadingIndicatorTrack =
    'M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z'
export const loadingIndicatorArc =
    'M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z'

export function getLoadingIndicatorClasses(
    size: LoadingIndicatorSize = 'md',
): string {
    return `fi-icon fi-loading-indicator fi-size-${size}`
}
