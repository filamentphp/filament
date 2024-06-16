document.addEventListener('alpine:init', () => {
    window.Alpine.data('filamentSchema', ({ livewireId }) => ({
        getGetUtility: function (containerPath) {
            return (path, isAbsolute) => {
                let containerPathCopy = containerPath

                if (path.startsWith('/')) {
                    isAbsolute = true
                    path = path.slice(1)
                }

                if (isAbsolute) {
                    return this.$wire.$get(path)
                }

                while (path.startsWith('../')) {
                    containerPathCopy = containerPathCopy.includes('.')
                        ? containerPathCopy.slice(
                              0,
                              containerPathCopy.lastIndexOf('.'),
                          )
                        : null

                    path = path.slice(3)
                }

                if (['', null, undefined].includes(containerPathCopy)) {
                    return path
                }

                return this.$wire.$get(`${containerPathCopy}.${path}`)
            }
        },

        handleFormValidationError: function (event) {
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
    }))
})
