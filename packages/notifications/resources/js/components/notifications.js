const hosts = new Set()
const batches = new Map()
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
            // Livewire 3 transport failures are handled by `commit.fail` below.
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

function interceptNotificationRequest({ respond, succeed, fail }) {
    const requestGeneration = generation
    let batch = null
    let redirects = false

    const capture = (receipt, shouldRedirect) => {
        if (
            requestGeneration !== generation ||
            !batchPattern.test(receipt ?? '')
        )
            return

        batch = receipt
        redirects = shouldRedirect

        if (redirects) {
            // Livewire 3 applies redirects before `request.succeed`.
            remember(batch, { redirect: true, ready: false })
        }
    }
    const capturePayload = ({ json }) =>
        capture(
            json.filamentNotificationBatch,
            json.components.some((component) => component.effects?.redirect),
        )

    respond(({ response }) => {
        if (
            !response.ok ||
            response.redirected ||
            requestGeneration !== generation
        )
            return

        if (response.headers) {
            capture(
                response.headers.get('X-Filament-Notifications'),
                response.headers.get('X-Filament-Notifications-Redirect') ===
                    'true',
            )
        } else {
            // Livewire 3 replaces streamed responses with a headerless reader.
            // Observe its final JSON before effects, without consuming it twice.
            const read = response.text.bind(response)
            response.text = async () => {
                const text = await read()
                if (response.ok) capturePayload({ json: JSON.parse(text) })
                return text
            }
        }
    })

    succeed(({ json }) => {
        if (
            requestGeneration !== generation ||
            redirects ||
            json.components.some((component) => component.effects?.redirect)
        )
            return

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

    fail(() => {
        if (batch && redirects) {
            batches.delete(batch)
            saveHandoff()
        }
    })
}

export function setUpNotifications() {
    Livewire.hook('request', interceptNotificationRequest)
    Livewire.hook('commit', ({ component, commit, fail }) => {
        const attempt = flight

        if (
            attempt &&
            component.id === attempt.host.$wire.$id &&
            commit.calls.some(
                (call) =>
                    call.method === 'pullNotificationsFromSession' &&
                    JSON.stringify(call.params) ===
                        JSON.stringify([attempt.batches]),
            )
        ) {
            fail(() => settle(attempt))
        }
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
