import mask from '@alpinejs/mask'
import intersect from '@alpinejs/intersect'

import '../css/components/date-time-picker.css'
import '../css/components/file-upload.css'
import '../css/components/markdown-editor.css'
import '../css/components/rich-editor.css'
import '../css/components/select.css'
import '../css/components/tags-input.css'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(mask)
    window.Alpine.plugin(intersect)
})
