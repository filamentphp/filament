// import 'alpinejs'

import './alpine.js'
import 'focus-visible'
import {createPopper} from '@popperjs/core/lib/popper-lite.js'
import flip from '@popperjs/core/lib/modifiers/flip.js'
import preventOverflow from '@popperjs/core/lib/modifiers/preventOverflow.js'

window.createPopper = createPopper
window.flip = flip
window.preventOverflow = preventOverflow
