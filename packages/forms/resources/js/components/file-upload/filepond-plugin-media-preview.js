/**
 * Source: https://github.com/nielsboogaard/filepond-plugin-media-preview (MIT)
 * Changes:
 * - Merged all source modules into a single file.
 * - Fixed upstream PR #33 `mediaPreviewHeight` implementation.
 */

const isPreviewableVideo = (file) => /^video/.test(file.type)

const isPreviewableAudio = (file) => /^audio/.test(file.type)

class AudioPlayer {
    constructor(mediaEl, audioElements) {
        this.mediaEl = mediaEl
        this.audioElements = audioElements
        this.onPlayhead = false
        this.duration = 0
        this.timelineWidth =
            this.audioElements.timeline.offsetWidth -
            this.audioElements.playhead.offsetWidth
        this.movePlayheadHandler = this.movePlayhead.bind(this)

        this.registerListeners()
    }

    registerListeners() {
        this.mediaEl.addEventListener(
            'timeupdate',
            this.timeUpdate.bind(this),
            false,
        )
        this.mediaEl.addEventListener(
            'canplaythrough',
            () => (this.duration = this.mediaEl.duration),
            false,
        )
        this.audioElements.timeline.addEventListener(
            'click',
            this.timelineClicked.bind(this),
            false,
        )
        this.audioElements.button.addEventListener(
            'click',
            this.play.bind(this),
        )
        this.audioElements.playhead.addEventListener(
            'mousedown',
            this.mouseDown.bind(this),
            false,
        )
        window.addEventListener('mouseup', this.mouseUp.bind(this), false)
    }

    play() {
        if (this.mediaEl.paused) {
            this.mediaEl.play()
        } else {
            this.mediaEl.pause()
        }

        this.audioElements.button.classList.toggle('play')
        this.audioElements.button.classList.toggle('pause')
    }

    timeUpdate() {
        const playPercent = (this.mediaEl.currentTime / this.duration) * 100

        this.audioElements.playhead.style.marginLeft = `${playPercent}%`

        if (this.mediaEl.currentTime === this.duration) {
            this.audioElements.button.classList.toggle('play')
            this.audioElements.button.classList.toggle('pause')
        }
    }

    movePlayhead(event) {
        const newMarginLeft =
            event.clientX - this.getPosition(this.audioElements.timeline)

        if (newMarginLeft >= 0 && newMarginLeft <= this.timelineWidth) {
            this.audioElements.playhead.style.marginLeft = `${newMarginLeft}px`
        }

        if (newMarginLeft < 0) {
            this.audioElements.playhead.style.marginLeft = '0px'
        }

        if (newMarginLeft > this.timelineWidth) {
            this.audioElements.playhead.style.marginLeft = `${this.timelineWidth - 4}px`
        }
    }

    timelineClicked(event) {
        this.movePlayhead(event)
        this.mediaEl.currentTime = this.duration * this.clickPercent(event)
    }

    mouseDown() {
        this.onPlayhead = true
        window.addEventListener('mousemove', this.movePlayheadHandler, true)
        this.mediaEl.removeEventListener(
            'timeupdate',
            this.timeUpdate.bind(this),
            false,
        )
    }

    mouseUp(event) {
        window.removeEventListener('mousemove', this.movePlayheadHandler, true)

        if (this.onPlayhead) {
            this.movePlayhead(event)
            this.mediaEl.currentTime = this.duration * this.clickPercent(event)
            this.mediaEl.addEventListener(
                'timeupdate',
                this.timeUpdate.bind(this),
                false,
            )
        }

        this.onPlayhead = false
    }

    clickPercent(event) {
        return (
            (event.clientX - this.getPosition(this.audioElements.timeline)) /
            this.timelineWidth
        )
    }

    getPosition(element) {
        return element.getBoundingClientRect().left
    }
}

const createMediaView = (_) =>
    _.utils.createView({
        name: 'media-preview',
        tag: 'div',
        ignoreRect: true,
        create: ({ root, props }) => {
            const item = root.query('GET_ITEM', { id: props.id })
            const tagName = isPreviewableAudio(item.file) ? 'audio' : 'video'

            root.ref.media = document.createElement(tagName)
            root.ref.media.setAttribute('controls', true)
            root.element.appendChild(root.ref.media)

            if (isPreviewableAudio(item.file)) {
                const fragment = document.createDocumentFragment()

                root.ref.audio = []
                root.ref.audio.container = document.createElement('div')
                root.ref.audio.button = document.createElement('span')
                root.ref.audio.timeline = document.createElement('div')
                root.ref.audio.playhead = document.createElement('div')

                root.ref.audio.container.className = 'audioplayer'
                root.ref.audio.button.className = 'playpausebtn play'
                root.ref.audio.timeline.className = 'timeline'
                root.ref.audio.playhead.className = 'playhead'

                root.ref.audio.timeline.appendChild(root.ref.audio.playhead)
                root.ref.audio.container.appendChild(root.ref.audio.button)
                root.ref.audio.container.appendChild(root.ref.audio.timeline)
                fragment.appendChild(root.ref.audio.container)

                root.element.appendChild(fragment)
            }
        },
        write: _.utils.createRoute({
            DID_MEDIA_PREVIEW_LOAD: ({ root, props }) => {
                const { id, mediaPreviewHeight } = props
                const item = root.query('GET_ITEM', { id })

                if (!item) {
                    return
                }

                const url = window.URL || window.webkitURL
                const blob = new Blob([item.file], { type: item.file.type })

                root.ref.media.type = item.file.type
                root.ref.media.src =
                    (item.file.mock && item.file.url) ||
                    url.createObjectURL(blob)

                if (isPreviewableAudio(item.file)) {
                    new AudioPlayer(root.ref.media, root.ref.audio)
                }

                root.ref.media.addEventListener(
                    'loadeddata',
                    () => {
                        let height = 75

                        if (isPreviewableVideo(item.file)) {
                            if (mediaPreviewHeight) {
                                height = mediaPreviewHeight
                                root.element.querySelector(
                                    'video',
                                ).style.height = `${height}px`
                            } else {
                                let containerWidth = root.ref.media.offsetWidth
                                let factor =
                                    root.ref.media.videoWidth / containerWidth

                                height = root.ref.media.videoHeight / factor
                            }
                        }

                        root.dispatch('DID_UPDATE_PANEL_HEIGHT', {
                            id,
                            height,
                        })
                    },
                    false,
                )
            },
        }),
    })

const createMediaWrapperView = (_) => {
    const didCreatePreviewContainer = ({ root, props }) => {
        const item = root.query('GET_ITEM', props.id)

        if (!item) {
            return
        }

        root.dispatch('DID_MEDIA_PREVIEW_LOAD', {
            id: props.id,
            mediaPreviewHeight: props.mediaPreviewHeight,
        })
    }

    const create = ({ root, props }) => {
        const media = createMediaView(_)

        root.ref.media = root.appendChildView(
            root.createChildView(media, {
                id: props.id,
                mediaPreviewHeight: props.mediaPreviewHeight,
            }),
        )
    }

    return _.utils.createView({
        name: 'media-preview-wrapper',
        create,
        write: _.utils.createRoute({
            DID_MEDIA_PREVIEW_CONTAINER_CREATE: didCreatePreviewContainer,
        }),
    })
}

const plugin = (fpAPI) => {
    const { addFilter, utils } = fpAPI
    const { Type, createRoute } = utils
    const mediaWrapperView = createMediaWrapperView(fpAPI)

    addFilter('CREATE_VIEW', (viewAPI) => {
        const { is, view, query } = viewAPI

        if (!is('file')) {
            return
        }

        const didLoadItem = ({ root, props }) => {
            const item = query('GET_ITEM', props.id)
            const allowVideoPreview = query('GET_ALLOW_VIDEO_PREVIEW')
            const allowAudioPreview = query('GET_ALLOW_AUDIO_PREVIEW')
            const mediaPreviewHeight = query('GET_MEDIA_PREVIEW_HEIGHT')

            if (
                !item ||
                item.archived ||
                ((!isPreviewableVideo(item.file) || !allowVideoPreview) &&
                    (!isPreviewableAudio(item.file) || !allowAudioPreview))
            ) {
                return
            }

            root.ref.mediaPreview = view.appendChildView(
                view.createChildView(mediaWrapperView, {
                    id: props.id,
                    mediaPreviewHeight,
                }),
            )

            root.dispatch('DID_MEDIA_PREVIEW_CONTAINER_CREATE', {
                id: props.id,
            })
        }

        view.registerWriter(
            createRoute(
                {
                    DID_LOAD_ITEM: didLoadItem,
                },
                ({ root, props }) => {
                    const item = query('GET_ITEM', props.id)
                    const allowVideoPreview = root.query(
                        'GET_ALLOW_VIDEO_PREVIEW',
                    )
                    const allowAudioPreview = root.query(
                        'GET_ALLOW_AUDIO_PREVIEW',
                    )

                    if (
                        !item ||
                        ((!isPreviewableVideo(item.file) ||
                            !allowVideoPreview) &&
                            (!isPreviewableAudio(item.file) ||
                                !allowAudioPreview)) ||
                        root.rect.element.hidden
                    ) {
                        return
                    }
                },
            ),
        )
    })

    return {
        options: {
            allowVideoPreview: [true, Type.BOOLEAN],
            allowAudioPreview: [true, Type.BOOLEAN],
            mediaPreviewHeight: [null, Type.INT],
        },
    }
}

if (typeof window !== 'undefined' && typeof window.document !== 'undefined') {
    document.dispatchEvent(
        new CustomEvent('FilePond:pluginloaded', { detail: plugin }),
    )
}

export default plugin
