import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import AlpineLazyLoadAssets from 'alpine-lazy-load-assets'
import AsyncAlpine from 'async-alpine'
import { md5 } from 'js-md5'
import Sortable from './sortable.js'
import Tooltip from '@ryangjchandler/alpine-tooltip'
import './tooltip.js'
import dropdown from './components/dropdown.js'
import formButton from './components/form-button.js'
import modal from './components/modal.js'
import oneTimeCodeInput from './components/one-time-code.js'
import './disabled-button.js'
import copyable from './components/copyable.js'
import './partials.js'
import pluralize from './utilities/pluralize.js'

import 'tippy.js/dist/tippy.css'
import 'tippy.js/themes/light.css'

document.addEventListener('alpine:init', () => {
    // Temporary compatibility fix. Remove once the minimum supported Livewire
    // version bundles Alpine's fix: https://github.com/alpinejs/alpine/pull/4922
    window.Alpine.interceptClone((existingElement, incomingElement) => {
        // Preserve cloaks on elements still waiting for initialization.
        if (existingElement.hasAttribute?.('x-cloak') === false) {
            incomingElement.removeAttribute?.('x-cloak')
        }
    })

    window.Alpine.plugin(AlpineFloatingUI)
    window.Alpine.plugin(AlpineLazyLoadAssets)
    window.Alpine.plugin(AsyncAlpine)
    window.Alpine.plugin(Sortable)
    window.Alpine.plugin(Tooltip)
    window.Alpine.data('filamentDropdown', dropdown)
    window.Alpine.data('filamentFormButton', formButton)
    window.Alpine.data('filamentModal', modal)
    window.Alpine.data('filamentOneTimeCodeInput', oneTimeCodeInput)
    window.Alpine.data('filamentCopyable', copyable)
})

window.jsMd5 = md5
window.pluralize = pluralize
