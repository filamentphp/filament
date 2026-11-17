export default () => ({
    openModalFromTrigger(trigger, openEventName) {
        trigger.nextElementSibling.dispatchEvent(new CustomEvent(openEventName))
    },
})
