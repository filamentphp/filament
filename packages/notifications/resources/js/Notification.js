import { v4 as uuid } from 'uuid-browser'

class Notification {
    constructor() {
        this.id(uuid())

        return this
    }

    id(id) {
        this.id = id

        return this
    }

    title(title) {
        this.title = title

        return this
    }

    body(body) {
        this.body = body

        return this
    }

    actions(actions) {
        this.actions = actions

        return this
    }

    status(status) {
        this.status = status

        return this
    }

    color(color) {
        this.color = color

        return this
    }

    icon(icon) {
        this.icon = icon

        return this
    }

    iconColor(color) {
        this.iconColor = color

        return this
    }

    duration(duration) {
        this.duration = duration

        return this
    }

    seconds(seconds) {
        this.duration(seconds * 1000)

        return this
    }

    persistent() {
        this.duration('persistent')

        return this
    }

    danger() {
        this.status('danger')

        return this
    }

    info() {
        this.status('info')

        return this
    }

    success() {
        this.status('success')

        return this
    }

    warning() {
        this.status('warning')

        return this
    }

    view(view) {
        this.view = view

        return this
    }

    viewData(viewData) {
        this.viewData = viewData

        return this
    }

    send() {
        window.dispatchEvent(
            new CustomEvent('notificationSent', {
                detail: {
                    notification: this,
                },
            }),
        )

        return this
    }
}

class Action {
    constructor(name) {
        this.name(name)

        return this
    }

    name(name) {
        this.name = name

        return this
    }

    color(color) {
        this.color = color

        return this
    }

    dispatch(event, data) {
        this.event(event)
        this.eventData(data)

        return this
    }

    dispatchSelf(event, data) {
        this.dispatch(event, data)
        this.dispatchDirection = 'self'

        return this
    }

    dispatchTo(component, event, data) {
        this.dispatch(event, data)
        this.dispatchDirection = 'to'
        this.dispatchToComponent = component

        return this
    }

    /**
     * @deprecated Use `dispatch()` instead.
     */
    emit(event, data) {
        this.dispatch(event, data)

        return this
    }

    /**
     * @deprecated Use `dispatchSelf()` instead.
     */
    emitSelf(event, data) {
        this.dispatchSelf(event, data)

        return this
    }

    /**
     * @deprecated Use `dispatchTo()` instead.
     */
    emitTo(component, event, data) {
        this.dispatchTo(component, event, data)

        return this
    }

    dispatchDirection(dispatchDirection) {
        this.dispatchDirection = dispatchDirection

        return this
    }

    dispatchToComponent(component) {
        this.dispatchToComponent = component

        return this
    }

    event(event) {
        this.event = event

        return this
    }

    eventData(data) {
        this.eventData = data

        return this
    }

    extraAttributes(attributes) {
        this.extraAttributes = attributes

        return this
    }

    icon(icon) {
        this.icon = icon

        return this
    }

    iconPosition(position) {
        this.iconPosition = position

        return this
    }

    outlined(condition = true) {
        this.isOutlined = condition

        return this
    }

    disabled(condition = true) {
        this.isDisabled = condition

        return this
    }

    label(label) {
        this.label = label

        return this
    }

    close(condition = true) {
        this.shouldClose = condition

        return this
    }

    openUrlInNewTab(condition = true) {
        this.shouldOpenUrlInNewTab = condition

        return this
    }

    size(size) {
        this.size = size

        return this
    }

    url(url) {
        this.url = url

        return this
    }

    view(view) {
        this.view = view

        return this
    }

    button() {
        this.view('filament-actions::button-action')

        return this
    }

    grouped() {
        this.view('filament-actions::grouped-action')

        return this
    }

    link() {
        this.view('filament-actions::link-action')

        return this
    }
}

class ActionGroup {
    constructor(actions) {
        this.actions(actions)

        return this
    }

    actions(actions) {
        this.actions = actions.map((action) => action.grouped())

        return this
    }

    color(color) {
        this.color = color

        return this
    }

    icon(icon) {
        this.icon = icon

        return this
    }

    iconPosition(position) {
        this.iconPosition = position

        return this
    }

    label(label) {
        this.label = label

        return this
    }

    tooltip(tooltip) {
        this.tooltip = tooltip

        return this
    }
}

export { Action, ActionGroup, Notification }
