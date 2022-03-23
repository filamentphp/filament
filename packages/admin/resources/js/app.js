import '../css/app.css'

import Alpine from 'alpinejs'
import Chart from 'chart.js/auto'

import Collapse from '@alpinejs/collapse'
import FormsAlpinePlugin from '../../../forms/dist/module.esm'
import Focus from '@alpinejs/focus'
import Persist from '@alpinejs/persist'
import Tooltip from '@ryangjchandler/alpine-tooltip'

import {dispatch as $dispatch} from 'alpinejs/src/utils/dispatch';

Alpine.plugin(Collapse)
Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(Focus)
Alpine.plugin(Persist)
Alpine.plugin(Tooltip)

Alpine.store('sidebar', {
    isOpen: Alpine.$persist(false).as('isOpen'),

    collapsedGroups: Alpine.$persist([]).as('collapsedGroups'),

    groupIsCollapsed(group) {
        return this.collapsedGroups.includes(group)
    },

    toggleCollapsedGroup(group) {
        this.collapsedGroups = this.collapsedGroups.includes(group) ?
            this.collapsedGroups.filter(g => g !== group) :
            this.collapsedGroups.concat(group)
    },

    close() {
        this.isOpen = false
    },

    open() {
        this.isOpen = true
    },
})

Alpine.store('theme', {
    currentTheme: null,

    mode: null,

    init: function () {
        this.currentTheme = localStorage.getItem('theme') || this.getSystemTheme()
        this.mode = localStorage.getItem('theme') ? 'manual' : 'auto'

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
            if (this.mode === 'manual') return

            if (event.matches && (! document.documentElement.classList.contains('dark'))) {
                this.currentTheme = 'dark'

                document.documentElement.classList.add('dark')
            } else if ((! event.matches) && document.documentElement.classList.contains('dark')) {
                this.currentTheme = 'light'

                document.documentElement.classList.remove('dark')
            }
        })

        Alpine.effect(() => {
            if (this.mode === 'auto') return

            if (this.currentTheme === 'dark' && (! document.documentElement.classList.contains('dark'))) {
                document.documentElement.classList.add('dark')

            } else if (this.currentTheme === 'light' && document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark')
            }

            $dispatch(window, 'dark-mode-toggled', this.currentTheme)
        })
    },

    toggle: function () {
        this.mode = 'manual'
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light'
    },

    isLight: function() {
        return this.currentTheme === 'light'
    },

    isDark: function() {
        return this.currentTheme === 'dark'
    },

    getSystemTheme: function() {
        return this.isSystemDark() ? 'dark' : 'light'
    },

    isSystemDark: function () {
        return window.matchMedia('(prefers-color-scheme: dark)').matches
    },
})

Chart.defaults.font.family = `'DM Sans', sans-serif`
Chart.defaults.color = '#6b7280'

window.Alpine = Alpine
window.Chart = Chart

Alpine.start()
