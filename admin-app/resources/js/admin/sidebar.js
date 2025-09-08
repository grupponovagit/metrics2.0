// Sidebar Interattiva - Gestione Moduli
document.addEventListener('DOMContentLoaded', function() {
    
    // Gestione doppio click con debounce
    let clickTimeout;
    
    document.addEventListener('click', function(e) {
        const moduleButton = e.target.closest('.module-button');
        if (!moduleButton) return;
        
        // Cancella il timeout precedente se presente
        if (clickTimeout) {
            clearTimeout(clickTimeout);
            clickTimeout = null;
        }
        
        // Aggiungi feedback visivo immediato
        moduleButton.style.transform = 'scale(0.95)';
        setTimeout(() => {
            moduleButton.style.transform = '';
        }, 150);
    });
    
    // Gestione doppio click
    document.addEventListener('dblclick', function(e) {
        const moduleButton = e.target.closest('.module-button');
        if (!moduleButton) return;
        
        // Cancella il click singolo
        if (clickTimeout) {
            clearTimeout(clickTimeout);
            clickTimeout = null;
        }
        
        // Aggiungi effetto di navigazione
        moduleButton.style.transform = 'scale(0.9)';
        moduleButton.style.filter = 'brightness(1.2)';
        
        setTimeout(() => {
            const url = moduleButton.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            }
        }, 100);
    });
    
    // Miglioramento accessibilità - keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            const focused = document.activeElement;
            if (focused && focused.classList.contains('module-button')) {
                e.preventDefault();
                focused.click();
            }
        }
    });
    
    // Auto-close dei sottomenu quando si naviga
    window.addEventListener('beforeunload', function() {
        // Salva stato se necessario
        const activeModule = document.querySelector('[x-data*="activeModule"]');
        if (activeModule) {
            const alpine = Alpine.$data(activeModule);
            if (alpine) {
                localStorage.setItem('sidebar-active-module', alpine.activeModule);
            }
        }
    });
    
    // Visual feedback migliorato
    const style = document.createElement('style');
    style.textContent = `
        .module-button:focus {
            outline: 2px solid hsl(var(--p));
            outline-offset: 2px;
        }
        
        .module-button:focus-visible {
            outline: 2px solid hsl(var(--p));
            outline-offset: 2px;
        }
        
        .submenu-enter {
            animation: submenuSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes submenuSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    `;
    document.head.appendChild(style);
});
