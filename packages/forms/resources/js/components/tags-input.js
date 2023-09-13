export default function tagsInputFormComponent({ state, splitKeys }) {
    return {
        newTag: '',

        state,

        createTag: function () {
            this.newTag = this.newTag.trim()

            if (this.newTag === '') {
                return
            }

            if (this.state.includes(this.newTag)) {
                this.newTag = ''

                return
            }

            this.state.push(this.newTag)

            this.newTag = ''
        },

        deleteTag: function (tagToDelete) {
            this.state = this.state.filter((tag) => tag !== tagToDelete)
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
