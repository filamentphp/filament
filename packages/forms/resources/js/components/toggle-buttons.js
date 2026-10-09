export default function toggleButtonsFormComponent({
    state,
    stateBindingModifiers,
}) {
    let isDestroyed = false
    let commitTimeout
    const isLiveOnBlur = stateBindingModifiers.includes('blur')
    const isLiveOnChange = stateBindingModifiers.some((modifier) =>
        ['live', 'lazy', 'change'].includes(modifier),
    )
    const isLiveDebounced = stateBindingModifiers.includes('debounce')
    const debounce = String(
        stateBindingModifiers[stateBindingModifiers.indexOf('debounce') + 1] ??
            250,
    )
    const liveDebounce =
        debounce.endsWith('s') && !debounce.endsWith('ms')
            ? Number.parseFloat(debounce) * 1000
            : Number.parseFloat(debounce)

    return {
        state,

        submissionLocked: false,

        submissionLockObserver: null,

        init() {
            // Mirror Livewire's native `wire:submit` lock without locking on
            // ordinary reactive requests or depending on the form's action name.
            this.submissionLocked = this.$refs.submissionLock.disabled
            this.submissionLockObserver = new MutationObserver(() => {
                this.submissionLocked = this.$refs.submissionLock.disabled
            })
            this.submissionLockObserver.observe(this.$refs.submissionLock, {
                attributes: true,
                attributeFilter: ['disabled'],
            })
        },

        isSelected(value) {
            return (
                ['string', 'number'].includes(typeof this.state) &&
                String(this.state) === value
            )
        },

        select(value) {
            if (this.$refs.submissionLock.disabled) {
                return
            }

            this.state = this.isSelected(value) ? null : value

            if (isLiveOnChange && !isLiveOnBlur) {
                this.$nextTick(() => {
                    if (isDestroyed) {
                        return
                    }

                    if (isLiveDebounced) {
                        clearTimeout(commitTimeout)
                        commitTimeout = setTimeout(() => {
                            if (!isDestroyed) {
                                this.$wire.$commit()
                            }
                        }, liveDebounce)
                    } else {
                        this.$wire.$commit()
                    }
                })
            }
        },

        blur() {
            if (isLiveOnBlur) {
                this.$wire.$commit()
            }
        },

        destroy() {
            isDestroyed = true
            clearTimeout(commitTimeout)
            this.submissionLockObserver?.disconnect()
        },
    }
}
