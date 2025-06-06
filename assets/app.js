import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import './bootstrap.js';
import './navbar.js';
import './filter.js';
import './admin-burger.js'


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', () => {
    flatpickr('.datepicker', {
      dateFormat: 'd/m/Y',
      allowInput: true
    })
  });



