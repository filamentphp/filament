class Notification {
    constructor() {
        // `crypto.randomUUID()` requires a secure context (HTTPS); fall back to
        // `crypto.getRandomValues()` which works in all contexts including HTTP.
        this.id(
            crypto.randomUUID?.() ??
                '10000000-1000-4000-8000-100000000000'.replace(/[018]/g, (c) =>
                    (
                        +c ^
                        (crypto.getRandomValues(new Uint8Array(1))[0] &
                            (15 >> (+c / 4)))
                    ).toString(16),
                ),
        )

        return this
    }

    id(id) {
        this._id = id

        return this
    }

    title(title) {
        this._title = title

        return this
    }

    body(body) {
        this._body = body

        return this
    }

    actions(actions) {
        this._actions = actions

        return this
    }

    status(status) {
        this._status = status

        return this
    }

    color(color) {
        this._color = color

        return this
    }

    icon(icon) {
        this._icon = icon

        return this
    }

    iconColor(color) {
        this._iconColor = color

        return this
    }

    duration(duration) {
        this._duration = duration

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
        this._view = view

        return this
    }

    viewData(viewData) {
        this._viewData = viewData

        return this
    }

    toJSON() {
        return {
            id: this._id,
            title: this._title,
            body: this._body,
            actions: this._actions?.map((action) => action.toJSON()),
            status: this._status,
            color: this._color,
            icon: this._icon,
            iconColor: this._iconColor,
            duration: this._duration,
            view: this._view,
            viewData: this._viewData,
        }
    }

    send() {
        window.dispatchEvent(
            new CustomEvent('notificationSent', {
                detail: {
                    notification: this.toJSON(),
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
        this._name = name

        return this
    }

    color(color) {
        this._color = color

        return this
    }

    dispatch(event, data) {
        this.event(event)
        this.eventData(data)

        return this
    }

    dispatchSelf(event, data) {
        this.dispatch(event, data)
        this._dispatchDirection = 'self'

        return this
    }

    dispatchTo(component, event, data) {
        this.dispatch(event, data)
        this._dispatchDirection = 'to'
        this._dispatchToComponent = component

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
        this._dispatchDirection = dispatchDirection

        return this
    }

    dispatchToComponent(component) {
        this._dispatchToComponent = component

        return this
    }

    event(event) {
        this._event = event

        return this
    }

    eventData(data) {
        this._eventData = data

        return this
    }

    extraAttributes(attributes) {
        this._extraAttributes = attributes

        return this
    }

    icon(icon) {
        this._icon = icon

        return this
    }

    iconPosition(position) {
        this._iconPosition = position

        return this
    }

    outlined(condition = true) {
        this._isOutlined = condition

        return this
    }

    disabled(condition = true) {
        this._isDisabled = condition

        return this
    }

    label(label) {
        this._label = label

        return this
    }

    close(condition = true) {
        this._shouldClose = condition

        return this
    }

    openUrlInNewTab(condition = true) {
        this._shouldOpenUrlInNewTab = condition

        return this
    }

    size(size) {
        this._size = size

        return this
    }

    url(url) {
        this._url = url

        return this
    }

    view(view) {
        this._view = view

        return this
    }

    button() {
        this.view('filament::components.button.index')

        return this
    }

    grouped() {
        this.view('filament::components.dropdown.list.item')

        return this
    }

    iconButton() {
        this.view('filament::components.icon-button')

        return this
    }

    link() {
        this.view('filament::components.link')

        return this
    }

    toJSON() {
        return {
            name: this._name,
            color: this._color,
            event: this._event,
            eventData: this._eventData,
            dispatchDirection: this._dispatchDirection,
            dispatchToComponent: this._dispatchToComponent,
            extraAttributes: this._extraAttributes,
            icon: this._icon,
            iconPosition: this._iconPosition,
            isOutlined: this._isOutlined,
            isDisabled: this._isDisabled,
            label: this._label,
            shouldClose: this._shouldClose,
            shouldOpenUrlInNewTab: this._shouldOpenUrlInNewTab,
            size: this._size,
            url: this._url,
            view: this._view,
        }
    }
}

class ActionGroup {
    constructor(actions) {
        this.actions(actions)

        return this
    }

    actions(actions) {
        this._actions = actions.map((action) => action.grouped())

        return this
    }

    color(color) {
        this._color = color

        return this
    }

    icon(icon) {
        this._icon = icon

        return this
    }

    iconPosition(position) {
        this._iconPosition = position

        return this
    }

    label(label) {
        this._label = label

        return this
    }

    tooltip(tooltip) {
        this._tooltip = tooltip

        return this
    }

    toJSON() {
        return {
            actions: this._actions?.map((action) => action.toJSON()),
            color: this._color,
            icon: this._icon,
            iconPosition: this._iconPosition,
            label: this._label,
            tooltip: this._tooltip,
        }
    }
}

export { Action, ActionGroup, Notification }
