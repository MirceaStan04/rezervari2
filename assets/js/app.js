(() => {
    const COOKIE_KEY = 'ef-theme';
    const root       = document.documentElement;
    const toggle     = document.querySelector('[data-theme-toggle]');

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        root.setAttribute('data-bs-theme', theme);
        // Cookie citit de PHP la fiecare request — tema corecta fara flash
        document.cookie = COOKIE_KEY + '=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
        if (toggle) {
            toggle.innerHTML = theme === 'dark'
                ? '<i class="bi bi-sun-fill"></i><span>Light</span>'
                : '<i class="bi bi-moon-stars-fill"></i><span>Dark</span>';
        }
    }

    // Sincronizeaza butonul cu tema deja setata de PHP in HTML
    applyTheme(root.getAttribute('data-theme') || 'dark');

    if (toggle) {
        toggle.addEventListener('click', () => {
            applyTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        });
    }
})();
