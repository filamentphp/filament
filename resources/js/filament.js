// import 'alpinejs'

// A custom build of Alpine is being used until #1160 (https://github.com/alpinejs/alpine/pull/1160)
// is merged in and released.
import './alpine.js'
import 'focus-visible'
import {createPopper} from '@popperjs/core/lib/popper-lite.js'
import flip from '@popperjs/core/lib/modifiers/flip.js'
import preventOverflow from '@popperjs/core/lib/modifiers/preventOverflow.js'

window.createPopper = createPopper
window.flip = flip
window.preventOverflow = preventOverflow
