const resolveRelativeStatePath = function (containerPath, path, isAbsolute) {
    let containerPathCopy = containerPath

    if (path.startsWith('/')) {
        isAbsolute = true
        path = path.slice(1)
    }

    if (isAbsolute) return path

    while (path.startsWith('../')) {
        containerPathCopy = containerPathCopy.includes('.')
            ? containerPathCopy.slice(0, containerPathCopy.lastIndexOf('.'))
            : null
        path = path.slice(3)
    }

    if (['', null, undefined].includes(containerPathCopy)) return path
    if (['', null, undefined].includes(path)) return containerPathCopy

    return `${containerPathCopy}.${path}`
}

const reservedUtilities = new Set([
    '$el',
    '$refs',
    '$store',
    '$watch',
    '$dispatch',
    '$nextTick',
    '$root',
    '$data',
    '$id',
    '$event',
    '$focus',
    '$persist',
    '$tooltip',
    '$wire',
    '$get',
    '$set',
    '$state',
    '$statePath',
    '$callSchemaComponentMethod',
    '$schemaComponentMethods',
])

export default function schemaComponent({
    key,
    exposedMethods = [],
    path,
    containerPath,
    $wire,
}) {
    const callSchemaComponentMethod = (method, argumentsObject = {}) =>
        $wire.callSchemaComponentMethod(key, method, argumentsObject)

    const methods = Object.fromEntries(
        exposedMethods
            .filter((method) => !reservedUtilities.has(`$${method}`))
            .map((method) => [
                `$${method}`,
                (argumentsObject = {}) =>
                    callSchemaComponentMethod(method, argumentsObject),
            ]),
    )

    return {
        ...methods,
        $schemaComponentMethods: methods,
        $callSchemaComponentMethod: callSchemaComponentMethod,
        $statePath: path,
        $get: (path, isAbsolute) =>
            $wire.$get(
                resolveRelativeStatePath(containerPath, path, isAbsolute),
            ),
        $set: (path, state, isAbsolute, isLive = false) =>
            $wire.$set(
                resolveRelativeStatePath(containerPath, path, isAbsolute),
                state,
                isLive,
            ),
        get $state() {
            return $wire.$get(path)
        },
    }
}
