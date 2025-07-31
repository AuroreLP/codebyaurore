import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import './bootstrap.js';
import './navbar.js';
import './filter.js';
import './admin-burger.js'


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', () => {
    // Éviter flatpickr sur la page contact
    if (!window.location.pathname.includes('/contact')) {
        const datepickers = document.querySelectorAll('.datepicker');
        if (datepickers.length > 0) {
            flatpickr('.datepicker', {
                dateFormat: 'd/m/Y',
                allowInput: true
            });
        }
    }
  });



