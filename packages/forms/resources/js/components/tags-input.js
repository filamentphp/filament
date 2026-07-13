export default function tagsInputFormComponent({
    state,
    splitKeys,
    tagAddedMessage,
    tagRemovedMessage,
}) {
    return {
        newTag: '',

        state,

        liveRegionClearTimeout: null,

        announce(message) {
            // A published pre-change view override has no `liveRegion` ref, so bail out
            // instead of throwing and breaking tag entry for those users.
            const liveRegion = this.$refs.liveRegion

            if (!liveRegion) {
                return
            }

            if (this.liveRegionClearTimeout !== null) {
                clearTimeout(this.liveRegionClearTimeout)
            }

            liveRegion.textContent = message

            // Clear the announcement once it has been read, so stale messages do not
            // remain reachable by the screen reader virtual cursor.
            this.liveRegionClearTimeout = setTimeout(() => {
                liveRegion.textContent = ''

                this.liveRegionClearTimeout = null
            }, 3000)
        },

        createTag() {
            this.newTag = this.newTag.trim()

            if (this.newTag === '') {
                return
            }

            if (this.state.includes(this.newTag)) {
                this.newTag = ''

                return
            }

            this.state.push(this.newTag)

            // A function replacement inserts the tag literally, so `$` sequences in it are not treated as substitution patterns by `String.replace()`. The message is optional so a published pre-change view override does not throw.
            this.announce(tagAddedMessage?.replace(':tag', () => this.newTag))

            this.newTag = ''
        },

        deleteTag(tagToDelete) {
            this.state = this.state.filter((tag) => tag !== tagToDelete)

            this.announce(tagRemovedMessage?.replace(':tag', () => tagToDelete))
        },

        reorderTags(event) {
            const reordered = this.state.splice(event.oldIndex, 1)[0]
            this.state.splice(event.newIndex, 0, reordered)

            this.state = [...this.state]
        },

        input: {
            ['x-on:blur']: 'createTag()',
            ['x-model']: 'newTag',
            ['x-on:keydown'](event) {
                if (['Enter', ...splitKeys].includes(event.key)) {
                    event.preventDefault()
                    event.stopPropagation()

                    this.createTag()
                }
            },
            ['x-on:paste']() {
                this.$nextTick(() => {
                    if (splitKeys.length === 0) {
                        this.createTag()

                        return
                    }

                    const pattern = splitKeys
                        .map((key) =>
                            key.replace(/[/\-\\^$*+?.()|[\]{}]/g, '\\$&'),
                        )
                        .join('|')

                    this.newTag
                        .split(new RegExp(pattern, 'g'))
                        .forEach((tag) => {
                            this.newTag = tag

                            this.createTag()
                        })
                })
            },
        },
    }
}
