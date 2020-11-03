import 'focus-visible'
import 'alpinejs'
import { createPopper } from '@popperjs/core/lib/popper-lite.js'
import preventOverflow from '@popperjs/core/lib/modifiers/preventOverflow.js'
import flip from '@popperjs/core/lib/modifiers/flip.js'

window.createPopper = createPopper
window.preventOverflow = preventOverflow
window.flip = flip
