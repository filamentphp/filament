export default ({
    collapsible,
    collapseByDefault,
    reorderable,
    reorderMethod,
    $wire,
}) => ({
    collapsible,
    collapseByDefault,
    reorderable,
    reorderMethod: reorderMethod ?? 'reorderTreeTable',
    expandedNodes: new Set(),
    collapsedNodes: new Set(),

    handleTreeReorder(event) {
        if (!this.reorderable) {
            return
        }

        if (!this.$refs.treeRoot) {
            return
        }

        if (event.currentTarget !== this.$refs.treeRoot) {
            return
        }

        const order = this.buildOrder(this.$refs.treeRoot)

        if (typeof this.$wire[this.reorderMethod] === 'function') {
            this.$wire[this.reorderMethod](order)
        } else if (typeof this.$wire.call === 'function') {
            this.$wire.call(this.reorderMethod, order)
        }
    },

    buildOrder(container, parentId = null) {
        const records = []
        const nodes = container.querySelectorAll(':scope > [data-tree-node]')

        nodes.forEach((node, index) => {
            const id = node.getAttribute('x-sortable-item')

            if (!id) {
                return
            }

            records.push({
                id,
                position: index + 1,
                parent: parentId,
            })

            const childrenContainer = node.querySelector(
                ':scope > [data-tree-children]',
            )

            if (childrenContainer) {
                records.push(...this.buildOrder(childrenContainer, id))
            }
        })

        return records
    },

    isCollapsed(id) {
        if (!this.collapsible) {
            return false
        }

        if (this.collapseByDefault) {
            return !this.expandedNodes.has(id)
        }

        return this.collapsedNodes.has(id)
    },

    toggleNode(id) {
        if (!this.collapsible) {
            return
        }

        if (this.collapseByDefault) {
            if (this.expandedNodes.has(id)) {
                this.expandedNodes.delete(id)
            } else {
                this.expandedNodes.add(id)
            }

            return
        }

        if (this.collapsedNodes.has(id)) {
            this.collapsedNodes.delete(id)
        } else {
            this.collapsedNodes.add(id)
        }
    },
})
