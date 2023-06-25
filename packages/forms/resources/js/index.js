import mask from '@alpinejs/mask'

import '../css/components/color-picker.css'

import '../css/components/date-time-picker.css'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.css'
import '../css/components/file-upload.css'

import 'easymde/dist/easymde.min.css'
import '../css/components/markdown-editor.css'

import 'trix/dist/trix.css'
import '../css/components/rich-editor.css'

import '../css/components/select.css'

import '../css/components/tags-input.css'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(mask)
})
