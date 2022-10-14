export default function tagsInputFormComponent({ state }) {
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
    }
}
