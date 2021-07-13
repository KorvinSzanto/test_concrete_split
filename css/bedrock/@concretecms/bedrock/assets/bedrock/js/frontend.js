import jQuery from 'jquery'
import 'bootstrap'
import './frontend/async-thumbnail-builder'
import './frontend/locations/country-data-link'
import './frontend/locations/country-stateprovince-link'

// Let us use Vue with our theme JS
import VueManager from '../../../assets/cms/js/vue/Manager'
VueManager.bindToWindow(window)

window.$ = window.jQuery = jQuery
