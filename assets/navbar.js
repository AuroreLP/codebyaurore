import 'core-js/stable'; // Importer les polyfills stables
import 'regenerator-runtime/runtime'; // Importer le polyfill pour les générateurs et async/await

console.log('navbar.js chargé');

document.addEventListener('DOMContentLoaded', () => {
  const navBurger = document.querySelector(".nav__burger");
  const navMenu = document.querySelector(".nav__menu");

  if (!navBurger || !navMenu) return; // Sécurité si l'élément n'existe 
  
  // Toujours s'assurer que le menu est FERMÉ au chargement de la page
  navMenu.classList.remove("show");

  // Ouvrir / Fermer le menu au clic sur le burger
  navBurger.addEventListener("click", function(event) {
    event.stopPropagation(); // empêche la fermeture immédiate:
    navMenu.classList.toggle("show"); // on ajoute la classe show ou on l'enlève
  });

  // Fermer le menu si on clique ailleurs
  document.addEventListener("click", function(event) {
    if (!navBurger.contains(event.target) && !navMenu.contains(event.target)) {
      navMenu.classList.remove("show");
    }
  });

  // Fermer le menu automatiquement après un clic sur un lien
  navMenu.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => {
      navMenu.classList.remove("show");
    });
  });
});
