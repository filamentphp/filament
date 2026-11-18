// Use FilePond's public admission hook to exercise asynchronous file loading.
window.uploadProcessingTest = {
    admissionObserved: false,
    releaseAdmission: null,
    requests: {},
}

document.addEventListener('FilePond:init', (event) => {
    if (!event.target.closest('[data-testid="avatar-upload"]')) {
        return
    }

    event.detail.pond.setOptions({
        beforeAddFile: () =>
            new Promise((resolve) => {
                window.uploadProcessingTest.releaseAdmission = () =>
                    resolve(true)
            }),
    })
})

document.addEventListener('FilePond:addfilestart', (event) => {
    if (event.target.closest('[data-testid="avatar-upload"]')) {
        window.uploadProcessingTest.admissionObserved = true
    }
})

// Keep the avatar pending until cancellation, without fabricating upload callbacks.
const originalSend = XMLHttpRequest.prototype.send
const originalAbort = XMLHttpRequest.prototype.abort

XMLHttpRequest.prototype.send = function (body) {
    const file =
        body instanceof FormData &&
        [...body.values()].find((value) => value instanceof File)

    if (!file || file.name !== 'avatar.png') {
        return originalSend.call(this, body)
    }

    window.uploadProcessingTest.requests[file.name] = this
}

XMLHttpRequest.prototype.abort = function () {
    for (const [name, pending] of Object.entries(
        window.uploadProcessingTest.requests,
    )) {
        if (pending === this) {
            delete window.uploadProcessingTest.requests[name]
        }
    }

    return originalAbort.call(this)
}

window.uploadProcessingTest.cleanup = () => {
    for (const pending of Object.values(window.uploadProcessingTest.requests)) {
        originalAbort.call(pending)
    }

    window.uploadProcessingTest.requests = {}
    XMLHttpRequest.prototype.send = originalSend
    XMLHttpRequest.prototype.abort = originalAbort
}
