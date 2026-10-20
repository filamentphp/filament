const hosts = new Set()
const batches = new Map()
const completions = new WeakMap()
const handoffKey = 'filament.notifications.redirect'
const batchPattern = /^[a-f0-9]{40}$/

let generation = 0
let navigating = false
let scheduled = false
let wake = 0
let handledWake = 0
let flight = null

try {
    const stored = JSON.parse(sessionStorage.getItem(handoffKey) ?? '[]')

    if (Array.isArray(stored)) {
        stored
            .filter(
                (batch) =>
                    typeof batch === 'string' && batchPattern.test(batch),
            )
            .slice(-50)
            .forEach((batch) =>
                batches.set(batch, { redirect: true, ready: true }),
            )
    }
} catch {}

function saveHandoff() {
    try {
        const redirected = [...batches]
            .filter(([, receipt]) => receipt.redirect)
            .map(([batch]) => batch)

        if (redirected.length) {
            sessionStorage.setItem(handoffKey, JSON.stringify(redirected))
        } else {
            sessionStorage.removeItem(handoffKey)
        }
    } catch {
        // Full-page redirect delivery is best effort when storage is unavailable.
    }
}

function remember(batch, receipt) {
    batches.set(batch, receipt)

    while (batches.size > 50) {
        batches.delete(batches.keys().next().value)
    }

    saveHandoff()
}

function settle(attempt) {
    if (flight !== attempt) return

    // A failed pull may already have consumed its batch, so do not retry it.
    attempt.batches.forEach((batch) => batches.delete(batch))
    handledWake = Math.max(handledWake, attempt.wake)
    flight = null
    saveHandoff()
    schedule()
}

function schedule() {
    if (scheduled) return

    scheduled = true
    const scheduledGeneration = generation

    window.Alpine.nextTick(() => {
        scheduled = false

        if (navigating || flight) return
        if (scheduledGeneration !== generation) {
            schedule()
            return
        }

        const host = [...hosts].find((host) => host.$el.isConnected)
        if (!host) return

        const eligible = [...batches]
            .filter(
                ([, receipt]) =>
                    receipt.ready &&
                    (receipt.redirect || receipt.generation === generation),
            )
            .map(([batch]) => batch)

        if (!eligible.length && wake === handledWake) return

        const attempt = { host, batches: eligible, wake }
        flight = attempt

        try {
            host.$wire.pullNotificationsFromSession(eligible).then(
                () => settle(attempt),
                () => settle(attempt),
            )
        } catch (error) {
            settle(attempt)
            throw error
        }
    })
}

function messageCompletion(message) {
    if (!completions.has(message)) {
        let finish
        const promise = new Promise((resolve) => (finish = resolve))
        completions.set(message, { promise, finish, redirected: false })
    }

    return completions.get(message)
}

function interceptNotificationRequest({
    request,
    onResponse,
    onSuccess,
    onError,
    onFailure,
}) {
    const requestGeneration = generation
    let batch = null

    const capture = (receipt, shouldRedirect) => {
        if (
            requestGeneration !== generation ||
            !batchPattern.test(receipt ?? '')
        )
            return

        batch = receipt

        if (shouldRedirect) {
            remember(batch, { redirect: true, ready: false })
        }
    }
    const discard = () => {
        if (!batch) return

        batches.delete(batch)
        saveHandoff()
    }

    for (const message of request.messages) {
        messageCompletion(message).promise.then(() => {
            if (request.hasAllCancelledMessages()) discard()
        })
    }

    onResponse(({ response }) => {
        if (
            !response.ok ||
            response.redirected ||
            requestGeneration !== generation
        )
            return

        capture(
            response.headers.get('X-Filament-Notifications'),
            response.headers.get('X-Filament-Notifications-Redirect') ===
                'true',
        )
    })

    onSuccess(({ json }) => {
        capture(
            json.filamentNotificationBatch,
            json.components.some((component) => component.effects?.redirect),
        )

        // Exclude messages cancelled before global interceptors were attached.
        const active = [...request.getActiveMessages()].map(messageCompletion)

        Promise.all(active.map((completion) => completion.promise)).then(() => {
            if (request.hasAllCancelledMessages()) {
                discard()
                return
            }

            if (active.some((completion) => completion.redirected)) return

            discard()
            if (requestGeneration !== generation) return

            if (batch)
                remember(batch, {
                    generation: requestGeneration,
                    redirect: false,
                    ready: true,
                })

            if (json.filamentNotifications) {
                wake++
                schedule()
            }
        })
    })

    onError(discard)
    onFailure(discard)
}

export function setUpNotifications() {
    Livewire.interceptRequest(interceptNotificationRequest)
    Livewire.interceptMessage(({ message, onSuccess, onFinish }) => {
        const completion = messageCompletion(message)

        onSuccess(({ payload, onEffect }) => {
            onEffect(() => {
                completion.redirected = Boolean(payload.effects?.redirect)
            })
        })

        onFinish(completion.finish)
    })

    document.addEventListener('livewire:navigating', () => {
        navigating = true
        generation++

        for (const [batch, receipt] of batches) {
            if (!receipt.redirect) batches.delete(batch)
        }

        if (flight) settle(flight)
    })

    document.addEventListener('livewire:navigated', () => {
        navigating = false

        for (const receipt of batches.values()) {
            if (receipt.redirect) receipt.ready = true
        }

        wake++
        schedule()
    })
}

export default function notificationsComponent() {
    let host

    return {
        init() {
            host = this
            hosts.add(host)
            wake++
            schedule()
        },

        destroy() {
            hosts.delete(host)

            if (flight?.host === host) settle(flight)
        },
    }
}
