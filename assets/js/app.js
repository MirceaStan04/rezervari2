(() => {
    const STORAGE_KEY = 'ef-theme';
    const root   = document.documentElement;
    const toggle = document.querySelector('[data-theme-toggle]');

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        if (toggle) {
            toggle.innerHTML = theme === 'dark'
                ? '<i class="bi bi-sun-fill"></i><span>Light</span>'
                : '<i class="bi bi-moon-stars-fill"></i><span>Dark</span>';
        }
    }

    // Tema e deja aplicata de scriptul din <head>; actualizam doar iconita
    applyTheme(root.getAttribute('data-theme') || 'dark');

    if (toggle) {
        toggle.addEventListener('click', () => {
            applyTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        });
    }
})();
