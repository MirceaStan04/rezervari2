(() => {
  const root = document.documentElement;
  const storageKey = 'crimson-theme';
  const toggle = document.querySelector('[data-theme-toggle]');

  const applyTheme = (theme) => {
    root.setAttribute('data-theme', theme);
    localStorage.setItem(storageKey, theme);
    if (toggle) {
      toggle.innerHTML = theme === 'dark'
        ? '<i class="bi bi-sun-fill"></i><span>Light</span>'
        : '<i class="bi bi-moon-stars-fill"></i><span>Dark</span>';
    }
  };

  const storedTheme = localStorage.getItem(storageKey);
  applyTheme(storedTheme || 'dark');

  if (toggle) {
    toggle.addEventListener('click', () => {
      const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(nextTheme);
    });
  }
})();
