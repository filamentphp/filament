import 'alpinejs'
import Turbolinks from 'turbolinks'
import './lib/cookies'

Turbolinks.start()

window.checkDarkMode = () => {
  if (getCookie('filament_color_scheme')) {
    return getCookie('filament_color_scheme') === 'dark'
  }

  if (
    window.matchMedia &&
    window.matchMedia('(prefers-color-scheme: dark)').matches
  ) {
    return true
  }
  return false
}

if (checkDarkMode()) {
  document.documentElement.classList.add('mode-dark')
} else {
  document.documentElement.classList.remove('mode-dark')
}
