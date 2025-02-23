export default () => ({
    isOpen: window.Alpine.$persist(true).as('isOpen'),

    collapsedGroups: window.Alpine.$persist(null).as('collapsedGroups'),

    groupIsCollapsed: function (group) {
        return this.collapsedGroups.includes(group)
    },

    collapseGroup: function (group) {
        if (this.collapsedGroups.includes(group)) {
            return
        }

        this.collapsedGroups = this.collapsedGroups.concat(group)
    },

    toggleCollapsedGroup: function (group) {
        this.collapsedGroups = this.collapsedGroups.includes(group)
            ? this.collapsedGroups.filter(
                  (collapsedGroup) => collapsedGroup !== group,
              )
            : this.collapsedGroups.concat(group)
    },

    close: function () {
        this.isOpen = false
    },

    open: function () {
        this.isOpen = true
    },
})
