import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import AlpineLazyLoadAssets from 'alpine-lazy-load-assets'
import AsyncAlpine from 'async-alpine'
import { md5 } from 'js-md5'
import Sortable from './sortable'
import Tooltip from '@ryangjchandler/alpine-tooltip'
import dropdown from './components/dropdown.js'
import formButton from './components/form-button.js'
import modal from './components/modal.js'
import './partials.js'
import pluralize from './utilities/pluralize.js'

import 'tippy.js/dist/tippy.css'
import 'tippy.js/themes/light.css'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(AlpineFloatingUI)
    window.Alpine.plugin(AlpineLazyLoadAssets)
    window.Alpine.plugin(AsyncAlpine)
    window.Alpine.plugin(Sortable)
    window.Alpine.plugin(Tooltip)
    window.Alpine.data('filamentDropdown', dropdown)
    window.Alpine.data('filamentFormButton', formButton)
    window.Alpine.data('filamentModal', modal)
})

window.jsMd5 = md5
window.pluralize = pluralize
