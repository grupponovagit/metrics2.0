import './bootstrap';
import '../cruscotto-produzione-datepicker';

// === GESTIONE TEMA CENTRALIZZATA ===

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

// Funzione per sincronizzare tutti i toggle theme
function syncThemeToggles(theme) {
    const isDark = (theme === 'dark');
    const toggleInputs = document.querySelectorAll('.theme-toggle-input');
    
    toggleInputs.forEach(input => {
        input.checked = isDark;
    });
}

// Funzione per ottenere il tema corrente
function getCurrentTheme() {
    return localStorage.getItem('theme') || 
           document.documentElement.getAttribute('data-theme') || 
           'light';
}

// Funzione per applicare il tema
function applyTheme(theme) {
    console.log('🎨 Applying theme:', theme);
    
    localStorage.setItem('theme', theme);
    document.documentElement.setAttribute('data-theme', theme);
    updateLogos(theme);
    syncThemeToggles(theme);
}

// Funzione per fare toggle del tema
function toggleTheme() {
    const currentTheme = getCurrentTheme();
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
}

// Setup tema iniziale e gestione click
document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = getCurrentTheme();
    
    // Applica tema iniziale
    applyTheme(currentTheme);
    
    // Gestione click sui toggle button
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.theme-toggle-btn');
        if (toggleBtn) {
            e.preventDefault();
            toggleTheme();
        }
    });
    
    console.log('✅ Theme System Initialized - Current:', currentTheme);
});

// Avvia Alpine
window.Alpine.start();