import './bootstrap';
import { themeChange } from 'theme-change';

// Inizializza theme-change per DaisyUI
themeChange();

// Funzione per aggiornare i loghi in base al tema
function updateLogos(theme) {
    const lightLogos = document.querySelectorAll('#logo-light-theme, #nav-logo-light-theme, .logo-light');
    const darkLogos = document.querySelectorAll('#logo-dark-theme, #nav-logo-dark-theme, .logo-dark');
    
    if (theme === 'dark') {
        lightLogos.forEach(logo => logo.classList.add('hidden'));
        darkLogos.forEach(logo => logo.classList.remove('hidden'));
    } else {
        lightLogos.forEach(logo => logo.classList.remove('hidden'));
        darkLogos.forEach(logo => logo.classList.add('hidden'));
    }
}

// Setup tema iniziale
document.addEventListener('DOMContentLoaded', function() {
    const selectedTheme = localStorage.getItem("theme") || 'light';
    updateLogos(selectedTheme);
    document.documentElement.setAttribute('data-theme', selectedTheme);
});

// Gestione cambiamenti tema DaisyUI
document.addEventListener('click', function(e) {
    if (e.target.matches('[data-set-theme]')) {
        const newTheme = e.target.getAttribute('data-set-theme');
        setTimeout(() => {
            updateLogos(newTheme);
        }, 100);
    }
});

// Listener per eventi tema personalizzati
window.addEventListener('theme-changed', function(e) {
    updateLogos(e.detail.theme);
});

// Avvia Alpine
window.Alpine.start();

// App JS caricato - Tailwind + DaisyUI + Alpine.js