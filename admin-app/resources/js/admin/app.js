import './bootstrap';

import { themeChange } from 'theme-change';

themeChange();

// Funzione per aggiornare i loghi in base al tema
function updateLogos(theme) {
    const lightLogos = document.querySelectorAll('#logo-light-theme, #nav-logo-light-theme');
    const darkLogos = document.querySelectorAll('#logo-dark-theme, #nav-logo-dark-theme');
    
    if (theme === 'dark') {
        lightLogos.forEach(logo => logo.classList.add('hidden'));
        darkLogos.forEach(logo => logo.classList.remove('hidden'));
    } else {
        lightLogos.forEach(logo => logo.classList.remove('hidden'));
        darkLogos.forEach(logo => logo.classList.add('hidden'));
    }
}

// Imposta il tema iniziale
var selectedTheme = localStorage.getItem("theme") || 'light';
if(selectedTheme === 'dark') {
    document.getElementById("theme-change").checked = true;
}

// Aggiorna i loghi al caricamento della pagina
document.addEventListener('DOMContentLoaded', function() {
    updateLogos(selectedTheme);
});

// Ascolta i cambiamenti di tema
document.addEventListener('click', function(e) {
    if (e.target.matches('[data-set-theme]')) {
        const newTheme = e.target.getAttribute('data-set-theme');
        setTimeout(() => {
            updateLogos(newTheme);
        }, 100); // Piccolo ritardo per permettere al theme-change di completare
    }
});