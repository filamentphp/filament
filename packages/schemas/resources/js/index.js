import actions from './components/actions.js'

const resolveRelativeStatePath = function (containerPath, path, isAbsolute) {
    let containerPathCopy = containerPath

    if (path.startsWith('/')) {
        isAbsolute = true
        path = path.slice(1)
    }

    if (isAbsolute) {
        return path
    }

    while (path.startsWith('../')) {
        containerPathCopy = containerPathCopy.includes('.')
            ? containerPathCopy.slice(0, containerPathCopy.lastIndexOf('.'))
            : null

        path = path.slice(3)
    }

    if (['', null, undefined].includes(containerPathCopy)) {
        return path
    }

    if (['', null, undefined].includes(path)) {
        return containerPathCopy
    }

    return `${containerPathCopy}.${path}`
}

// NOTE: In some edge cases (e.g. DOM replaced/removed during a Livewire request
// lifecycle), relying on the `__livewire` property can cause
// "Could not find Livewire component in DOM tree." to be thrown.
// Using the closest `[wire:id]` is more resilient across Livewire versions.
const findClosestLivewireComponentId = (el) => {
    if (!el?.closest) {
        return null
    }

    return el.closest('[wire\\:id]')?.getAttribute('wire:id') ?? null
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentSchema', ({ livewireId }) => ({
        handleFormValidationError(event) {
            if (event.detail.livewireId !== livewireId) {
                return
            }

            this.$nextTick(() => {
                let error = this.$el.querySelector('[data-validation-error]')

                if (!error) {
                    return
                }

                let elementToExpand = error

                while (elementToExpand) {
                    elementToExpand.dispatchEvent(new CustomEvent('expand'))

                    elementToExpand = elementToExpand.parentNode
                }

                setTimeout(
                    () =>
                        error.closest('[data-field-wrapper]').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'start',
                        }),
                    200,
                )
            })
        },

        isStateChanged(state, old) {
            if (state === undefined) {
                return false
            }

            try {
                return JSON.stringify(state) !== JSON.stringify(old)
            } catch {
                return state !== old
            }
        },
    }))

    window.Alpine.data(
        'filamentSchemaComponent',
        ({ path, containerPath, $wire }) => ({
            $statePath: path,
            $get: (path, isAbsolute) => {
                return $wire.$get(
                    resolveRelativeStatePath(containerPath, path, isAbsolute),
                )
            },
            $set: (path, state, isAbsolute, isLive = false) => {
                return $wire.$set(
                    resolveRelativeStatePath(containerPath, path, isAbsolute),
                    state,
                    isLive,
                )
            },
            get $state() {
                return $wire.$get(path)
            },
        }),
    )

    window.Alpine.data('filamentActionsSchemaComponent', actions)

    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effects }) => {
            effects.dispatches?.forEach((dispatch) => {
                if (!dispatch.params?.awaitSchemaComponent) {
                    return
                }

                let els = Array.from(
                    component.el.querySelectorAll(
                        `[wire\\:partial="schema-component::${dispatch.params.awaitSchemaComponent}"]`,
                    ),
                ).filter(
                    (el) =>
                        findClosestLivewireComponentId(el) === component.id,
                )

                if (els.length === 1) {
                    return
                }

                if (els.length > 1) {
                    throw `Multiple schema components found with key [${dispatch.params.awaitSchemaComponent}].`
                }

                window.addEventListener(
                    `schema-component-${component.id}-${dispatch.params.awaitSchemaComponent}-loaded`,
                    () => {
                        window.dispatchEvent(
                            new CustomEvent(dispatch.name, {
                                detail: dispatch.params,
                            }),
                        )
                    },
                    { once: true },
                )
            })
        })
    })
})
