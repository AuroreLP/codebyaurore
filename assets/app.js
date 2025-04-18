import './bootstrap.js';
import flatpickr from "https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js";
import "https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css";
import './navbar.js';


/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', () => {
    flatpickr('.datepicker', {
      dateFormat: 'd/m/Y',
      allowInput: true
    })
  });



