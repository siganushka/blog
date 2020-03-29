import 'jquery'
import 'bootstrap'
import hljs from 'highlight.js'
import autosize from 'autosize'

/** exposed jquery global vars */
global.$ = global.jQuery = $

/** auto size for textarea */
autosize(document.querySelector('textarea'))

/** code high lighting for markdown */
hljs.initHighlightingOnLoad()
