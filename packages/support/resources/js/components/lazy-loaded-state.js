import { requestLazyLoadedState } from './lazy-loaded-state/requests.js'

const pendingGroups = new WeakMap()

export default (configuration) => {
    let component

    return {
        isLoading: true,
        hasFailed: false,
        isDestroyed: false,
        intersectionObserver: null,
        request: null,

        init() {
            component = this.$wire.__instance
            this.register()

            this.intersectionObserver = new IntersectionObserver((entries) => {
                if (
                    this.isDestroyed ||
                    !entries.some((entry) => entry.isIntersecting)
                ) {
                    return
                }

                this.intersectionObserver.disconnect()

                if (!this.request.isScheduled) {
                    this.load()
                }
            })

            this.intersectionObserver.observe(this.$el)
        },

        register() {
            let groups = pendingGroups.get(component)

            if (!groups) {
                groups = new Map()
                pendingGroups.set(component, groups)
            }

            const key = JSON.stringify([
                configuration.method,
                configuration.group,
            ])
            let group = groups.get(key)

            if (!group) {
                group = {
                    members: new Map(),
                    controller: new AbortController(),
                    isScheduled: false,
                }
                groups.set(key, group)
            }

            this.isLoading = true
            this.hasFailed = false
            this.request = group
            group.members.set(configuration.id, { owner: this, configuration })

            return group
        },

        load() {
            const group = this.register()

            if (group.isScheduled) {
                return
            }

            group.isScheduled = true

            // Include every rendered group member, not only those in the viewport.
            setTimeout(async () => {
                const groups = pendingGroups.get(component)
                const key = JSON.stringify([
                    configuration.method,
                    configuration.group,
                ])

                if (groups.get(key) === group) {
                    groups.delete(key)
                }

                if (!group.members.size) {
                    return
                }

                for (const { owner } of group.members.values()) {
                    owner.intersectionObserver?.disconnect()
                }

                const targets = Object.fromEntries(
                    Array.from(group.members, ([id, member]) => [
                        id,
                        member.configuration.target,
                    ]),
                )

                try {
                    const { html, isStale } = await requestLazyLoadedState(
                        component,
                        configuration,
                        targets,
                        group.controller,
                    )

                    for (const [id, { owner }] of group.members) {
                        if (owner.isDestroyed || !owner.$el.isConnected) {
                            continue
                        }

                        if (isStale) {
                            owner.register()
                            owner.intersectionObserver.observe(owner.$el)
                            continue
                        }

                        if (typeof html[id] !== 'string') {
                            throw new Error('Missing lazy-loaded state.')
                        }

                        const content = owner.$refs.content

                        Alpine.mutateDom(() => {
                            Alpine.destroyTree(content)
                            content.innerHTML = html[id]
                            Alpine.initTree(content)
                        })

                        owner.isLoading = false
                    }
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return
                    }

                    for (const { owner } of group.members.values()) {
                        if (owner.isDestroyed) {
                            continue
                        }

                        owner.isLoading = false
                        owner.hasFailed = true
                    }
                }
            }, 0)
        },

        destroy() {
            this.isDestroyed = true
            this.intersectionObserver?.disconnect()

            const group = Alpine.raw(this.request)
            group?.members.delete(configuration.id)

            if (group && !group.members.size) {
                group.controller.abort()

                const groups = pendingGroups.get(component)
                const key = JSON.stringify([
                    configuration.method,
                    configuration.group,
                ])

                if (groups?.get(key) === group) {
                    groups.delete(key)
                }
            }
        },
    }
}
