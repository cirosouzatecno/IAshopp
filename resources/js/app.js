import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const themeKey = 'theme';

const getTheme = () => {
    const stored = localStorage.getItem(themeKey);
    if (stored === 'dark' || stored === 'light') {
        return stored;
    }
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
};

const setTheme = (theme) => {
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    localStorage.setItem(themeKey, theme);
    document.querySelectorAll('[data-theme-label]').forEach((el) => {
        el.textContent = isDark ? 'Escuro' : 'Claro';
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setTheme(getTheme());
});

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]');
    if (!toggle) return;
    const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
    setTheme(next);
});
