export default ({ openEventName = 'open-modal' } = {}) => ({
    openModalFromTrigger(trigger) {
        trigger.nextElementSibling.dispatchEvent(new CustomEvent(openEventName))
    },
})
