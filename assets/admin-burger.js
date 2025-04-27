document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('adminBurger');
    const sidebar = document.getElementById('adminSidebar');

    burger.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        const expanded = sidebar.classList.contains('active');
        burger.setAttribute('aria-expanded', expanded);
    });
});
