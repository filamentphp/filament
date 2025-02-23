export default function actionsSchemaComponent() {
    return {
        isSticky: false,

        init: function () {
            this.evaluatePageScrollPosition()
        },

        evaluatePageScrollPosition: function () {
            this.isSticky =
                document.body.scrollHeight >=
                window.scrollY + window.innerHeight * 2
        },
    }
}
