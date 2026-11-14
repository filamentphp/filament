export default ({ language, state, view }) => [
    language.StreamLanguage.define({
        token(stream) {
            if (stream.match('server')) {
                return 'keyword'
            }

            stream.next()

            return null
        },
    }),
    language.indentUnit.of('    '),
    state.EditorState.tabSize.of(4),
    view.EditorView.contentAttributes.of({ 'data-custom-language': 'loaded' }),
]
