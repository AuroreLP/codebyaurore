import './bootstrap.js';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
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



