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
        switch (status) {
            case 'success':
                this.success()

                break

            case 'warning':
                this.warning()

                break

            case 'danger':
                this.danger()

                break
        }

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

    success() {
        this.icon('heroicon-o-check-circle')
        this.iconColor('success')

        return this
    }

    warning() {
        this.icon('heroicon-o-exclamation-circle')
        this.iconColor('warning')

        return this
    }

    danger() {
        this.icon('heroicon-o-x-circle')
        this.iconColor('danger')

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
        Livewire.emit('notificationSent', this)

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

    emit(event, data) {
        this.event(event)
        this.eventData(data)

        return this
    }

    emitSelf(event, data) {
        this.emit(event, data)
        this.emitDirection = 'self'

        return this
    }

    emitTo(component, event, data) {
        this.emit(event, data)
        this.emitDirection = 'to'
        this.emitToComponent = component

        return this
    }

    emitUp(event, data) {
        this.emit(event, data)
        this.emitDirection = 'up'

        return this
    }

    emitDirection(emitDirection) {
        this.emitDirection = emitDirection

        return this
    }

    emitToComponent(component) {
        this.emitToComponent = component

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
        this.shouldCloseNotification = condition

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
        this.view('notifications::actions.button-action')

        return this
    }

    grouped() {
        this.view('notifications::actions.grouped-action')

        return this
    }

    link() {
        this.view('notifications::actions.link-action')

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
