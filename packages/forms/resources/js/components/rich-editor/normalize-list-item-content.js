export default function normalizeListItemContent(node, schema) {
    if (!node || (!Array.isArray(node.content) && node.type !== 'listItem')) {
        return node
    }

    let content = (node.content ?? []).map((child) =>
        normalizeListItemContent(child, schema),
    )

    if (
        node.type === 'listItem' &&
        schema.nodes.listItem.spec.content === 'paragraph block*'
    ) {
        const normalizedContent = []
        let paragraph = null

        for (const child of content) {
            const childType = schema.nodes[child.type]

            if (!childType) {
                throw new Error(`Unknown rich editor node [${child.type}].`)
            }

            if (!childType.isInline) {
                normalizedContent.push(child)
                paragraph = null

                continue
            }

            if (!paragraph) {
                paragraph = { type: 'paragraph', content: [] }
                normalizedContent.push(paragraph)
            }

            paragraph.content.push(child)
        }

        if (normalizedContent[0]?.type !== 'paragraph') {
            normalizedContent.unshift({ type: 'paragraph', content: [] })
        }

        content = normalizedContent
    }

    return { ...node, content }
}
