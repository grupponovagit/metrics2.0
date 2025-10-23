/**
 * Loader Manager - Gestisce il loader durante navigazione e query
 */

class LoaderManager {
    constructor() {
        this.isLoading = false;
        this.loadingTimeout = null;
        this.minDisplayTime = 300; // Minimo 300ms per evitare flash
        this.startTime = null;
        
        this.init();
    }
    
    init() {
        // Intercetta click sui link - NON bloccare il comportamento di default
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && this.shouldShowLoader(link)) {
                // Mostra loader ma non bloccare la navigazione
                this.show();
                
                // Aggiungi timeout di sicurezza: se dopo 10 secondi il loader è ancora visibile, nascondilo
                setTimeout(() => {
                    if (this.isLoading) {
                        console.warn('[Loader] Timeout raggiunto, nascondo loader forzatamente');
                        this.forceHide();
                    }
                }, 10000);
            }
        }, false); // Cambiato a false per non usare capture phase
        
        // Intercetta submit dei form
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form && !form.hasAttribute('data-no-loader')) {
                this.show();
                
                // Timeout di sicurezza anche per i form
                setTimeout(() => {
                    if (this.isLoading) {
                        console.warn('[Loader] Timeout form raggiunto, nascondo loader forzatamente');
                        this.forceHide();
                    }
                }, 15000);
            }
        }, false);
        
        // Intercetta richieste AJAX (fetch)
        this.interceptFetch();
        
        // Intercetta richieste AJAX (XMLHttpRequest)
        this.interceptXHR();
        
        // Nascondi loader quando la pagina è caricata
        window.addEventListener('load', () => {
            this.hide();
        });
        
        // Nascondi loader se l'utente torna indietro
        window.addEventListener('pageshow', () => {
            this.hide();
        });
        
        // Nascondi loader se la pagina sta per essere scaricata
        window.addEventListener('beforeunload', () => {
            // Non nascondere qui, lascia che la nuova pagina lo gestisca
        });
    }
    
    shouldShowLoader(link) {
        const href = link.getAttribute('href');
        
        // Skip per link esterni
        if (href && href.startsWith('http') && !href.includes(window.location.host)) {
            return false;
        }
        
        // Skip per anchor link
        if (href && href.startsWith('#')) {
            return false;
        }
        
        // Skip se ha attributo data-no-loader
        if (link.hasAttribute('data-no-loader')) {
            return false;
        }
        
        // Skip per link con target _blank
        if (link.getAttribute('target') === '_blank') {
            return false;
        }
        
        return true;
    }
    
    show() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.startTime = Date.now();
        
        const loader = document.getElementById('app-loader');
        if (loader) {
            loader.classList.remove('hidden');
            loader.classList.add('flex');
            
            // Aggiungi classe al body per bloccare scroll
            document.body.style.overflow = 'hidden';
        }
        
        console.log('[Loader] Mostrato');
    }
    
    hide() {
        if (!this.isLoading) return;
        
        const elapsed = Date.now() - this.startTime;
        const remaining = Math.max(0, this.minDisplayTime - elapsed);
        
        // Assicura che il loader sia visibile per almeno il tempo minimo
        setTimeout(() => {
            const loader = document.getElementById('app-loader');
            if (loader) {
                loader.classList.add('hidden');
                loader.classList.remove('flex');
                
                // Ripristina scroll
                document.body.style.overflow = '';
            }
            
            this.isLoading = false;
            this.startTime = null;
            
            console.log('[Loader] Nascosto');
        }, remaining);
    }
    
    forceHide() {
        // Nascondi immediatamente senza aspettare
        const loader = document.getElementById('app-loader');
        if (loader) {
            loader.classList.add('hidden');
            loader.classList.remove('flex');
            
            // Ripristina scroll
            document.body.style.overflow = '';
        }
        
        this.isLoading = false;
        this.startTime = null;
        
        console.log('[Loader] Nascosto forzatamente');
    }
    
    interceptFetch() {
        const originalFetch = window.fetch;
        
        window.fetch = (...args) => {
            // Mostra loader per richieste non in background
            const options = args[1] || {};
            if (!options.background) {
                this.show();
            }
            
            return originalFetch(...args)
                .then(response => {
                    this.hide();
                    return response;
                })
                .catch(error => {
                    this.hide();
                    throw error;
                });
        };
    }
    
    interceptXHR() {
        const self = this;
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSend = XMLHttpRequest.prototype.send;
        
        XMLHttpRequest.prototype.open = function(...args) {
            this._url = args[1];
            return originalOpen.apply(this, args);
        };
        
        XMLHttpRequest.prototype.send = function(...args) {
            // Mostra loader
            if (!this._noLoader) {
                self.show();
            }
            
            // Nascondi loader quando completato
            this.addEventListener('loadend', () => {
                self.hide();
            });
            
            return originalSend.apply(this, args);
        };
    }
}

// Inizializza il loader manager
let loaderManager = null;

// Inizializza immediatamente o quando il DOM è pronto
function initLoaderManager() {
    if (!loaderManager) {
        loaderManager = new LoaderManager();
        console.log('[Loader Manager] Inizializzato');
        
        // Test: verifica che il loader esista
        const loader = document.getElementById('app-loader');
        if (loader) {
            console.log('[Loader Manager] Loader element trovato:', loader.id);
        } else {
            console.error('[Loader Manager] ERRORE: Loader element NON trovato!');
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLoaderManager);
} else {
    initLoaderManager();
}

// Export per uso esterno
export function showLoader() {
    if (loaderManager) {
        loaderManager.show();
    }
}

export function hideLoader() {
    if (loaderManager) {
        loaderManager.hide();
    }
}

// Helper globali per uso nei template
window.showLoader = showLoader;
window.hideLoader = hideLoader;

