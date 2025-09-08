// ============================================
// SIDEBAR ALPINE.JS COMPONENT
// ============================================

export default function sidebarComponent() {
    return {
        // Stato sidebar
        collapsed: false,
        activeModule: '',
        
        // Inizializzazione
        init() {
            // Carica stato da localStorage
            this.collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            this.activeModule = '';
            
            // Watch per persistenza
            this.$watch('collapsed', value => {
                localStorage.setItem('sidebar-collapsed', value);
                console.log('📱 Sidebar state:', value ? 'collapsed' : 'expanded');
            });
            
            this.$watch('activeModule', value => {
                localStorage.setItem('sidebar-active-module', value);
                console.log('📂 Active module:', value || 'none');
            });
            
            console.log('🚀 Sidebar initialized:', {
                collapsed: this.collapsed,
                activeModule: this.activeModule
            });
        },
        
        // Toggle sidebar collapse/expand
        toggleSidebar() {
            this.collapsed = !this.collapsed;
            // Chiudi tutti i dropdown quando si collassa
            if (this.collapsed) {
                this.activeModule = '';
            }
            console.log('🔄 Sidebar toggled:', this.collapsed ? 'collapsed' : 'expanded');
        },
        
        // Toggle modulo dropdown
        toggleModule(moduleKey) {
            console.log('🎯 Toggle module:', moduleKey, 'collapsed:', this.collapsed);
            
            // Se sidebar è collassata, aprila prima
            if (this.collapsed) {
                this.collapsed = false;
                // Aspetta che l'animazione finisca, poi apri il dropdown
                setTimeout(() => {
                    this.activeModule = moduleKey;
                    console.log('📂 Module opened after sidebar expand:', moduleKey);
                }, 150);
            } else {
                // Sidebar già aperta, toggle normale del dropdown
                if (this.activeModule === moduleKey) {
                    this.activeModule = '';
                    console.log('📂 Module closed:', moduleKey);
                } else {
                    this.activeModule = moduleKey;
                    console.log('📂 Module opened:', moduleKey);
                }
            }
        },
        
        // Verifica se modulo è expanded
        isModuleExpanded(moduleKey) {
            const expanded = this.activeModule === moduleKey;
            
            // Se siamo in una pagina di quel modulo, tienilo aperto anche
            const currentPath = window.location.pathname;
            const isCurrentModulePage = currentPath.includes(`/admin/${moduleKey}`);
            
            return expanded || isCurrentModulePage;
        },
        
        // Naviga a modulo (doppio click)
        navigateToModule(url) {
            if (url) {
                console.log('🔗 Navigating to:', url);
                window.location.href = url;
            }
        },
        
        // Rileva modulo attivo dalla URL
        detectActiveModule() {
            const path = window.location.pathname;
            const modules = ['home', 'hr', 'amministrazione', 'produzione', 'marketing', 'ict'];
            
            for (const module of modules) {
                if (path.includes(`/admin/${module}`)) {
                    console.log('🎯 Detected active module from URL:', module);
                    return module;
                }
            }
            return '';
        },
        
        // Tooltip text dinamico
        getTooltipText(text) {
            return this.collapsed ? text : '';
        },
        
        // Verifica se sidebar è collassata
        isSidebarCollapsed() {
            return this.collapsed;
        },
        
        // Verifica se sidebar è espansa
        isSidebarExpanded() {
            return !this.collapsed;
        }
    }
}
