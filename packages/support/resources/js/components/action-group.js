import {computePosition, flip, shift, autoUpdate} from '@floating-ui/dom'

export default (Alpine) => {
    Alpine.data('actionGroupTableComponent', ({
        mountTableActionUsing
    }) => {
        return {
            isOpen: false,

            mountTableActionUsing,

            cancelAutoUpdate: function () {},

            init: function () {
                //
            },

            update: function () {
                const trigger = this.$refs.trigger
                const dropdown = this.$refs.dropdown

                this.cancelAutoUpdate = autoUpdate(trigger, dropdown, () => {
                    computePosition( trigger, dropdown, {
                        placement: 'bottom-end',
                        middleware: [
                            flip(),
                            shift({ padding: 5 })
                        ],
                    }).then(({x, y}) => {
                        Object.assign(dropdown.style, {
                            left: `${x}px`,
                            top: `${y}px`,
                        })
                    })
                })
            },

            toggleDropdown: function () {
                this.isOpen = ! this.isOpen

                if( this.isOpen ){
                    this.update()
                } else {
                    this.cancelAutoUpdate()
                }
            },

            wireClickAction: function (actionName, tableRecordKey) {
                if(! actionName) return

                this.mountTableActionUsing(actionName, tableRecordKey)
            }
        }
    })
}
