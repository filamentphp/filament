export default function tagsInputFormComponent({
    state,
    splitKeys,
    tagAddedMessage,
    tagRemovedMessage,
}) {
    return {
        newTag: '',

        state,

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

            this.$refs.liveRegion.textContent = tagAddedMessage.replace(
                ':tag',
                this.newTag,
            )

            this.newTag = ''
        },

        deleteTag(tagToDelete) {
            this.state = this.state.filter((tag) => tag !== tagToDelete)

            this.$refs.liveRegion.textContent = tagRemovedMessage.replace(
                ':tag',
                tagToDelete,
            )
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
