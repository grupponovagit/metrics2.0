{{-- ============================================ --}}
{{-- SCRIPTS PER CRUSCOTTO PRODUZIONE          --}}
{{-- ============================================ --}}

<script>
    // ========================================== 
    // CONFIGURAZIONE COLONNE PER OGNI VISTA
    // ==========================================
    const tableColumns = {
        'dettagliato': [
            { key: 'prodotto', label: 'Prodotto' },
            { key: 'inserito', label: 'Inserito' },
            { key: 'ko', label: 'KO' },
            { key: 'backlog', label: 'BackLog' },
            { key: 'backlog_partner', label: 'BackLog Partner' },
            { key: 'ore', label: 'Ore' },
            { key: 'resa_prodotto', label: 'Resa Prod' },
            { key: 'resa_inserito', label: 'Resa Ins' },
            { key: 'resa_oraria', label: 'R/H' },
            { key: 'obiettivi', label: 'Obiettivi (tutti)' },
            { key: 'paf-mensile', label: 'PAF Mensile (tutti)' }
        ],
        'sintetico': [
            { key: 'prodotto', label: 'Prodotto' },
            { key: 'inserito', label: 'Inserito' },
            { key: 'ko', label: 'KO' },
            { key: 'backlog', label: 'BackLog' },
            { key: 'backlog_partner', label: 'BackLog Partner' },
            { key: 'ore', label: 'Ore' },
            { key: 'resa_prodotto', label: 'Resa Prod' },
            { key: 'resa_inserito', label: 'Resa Ins' },
            { key: 'resa_oraria', label: 'R/H' },
            { key: 'obiettivi', label: 'Obiettivi (tutti)' },
            { key: 'paf-mensile', label: 'PAF Mensile (tutti)' }
        ],
        'giornaliero': [
            { key: 'prodotto', label: 'Prodotto' },
            { key: 'inserito', label: 'Inserito' },
            { key: 'ko', label: 'KO' },
            { key: 'backlog', label: 'BackLog' },
            { key: 'backlog_partner', label: 'BackLog Partner' },
            { key: 'ore', label: 'Ore' },
            { key: 'resa_prodotto', label: 'Resa Prod' },
            { key: 'resa_inserito', label: 'Resa Ins' },
            { key: 'resa_oraria', label: 'R/H' },
            { key: 'obiettivi', label: 'Obiettivi (tutti)' },
            { key: 'paf-mensile', label: 'PAF Mensile (tutti)' }
        ]
    };
    
    let currentActiveView = 'sintetico';
    
    // ==========================================
    // SWITCH TRA VISTE (SINTETICO/DETTAGLIATO/GIORNALIERO)
    // ==========================================
    function switchView(view) {
        const tableDettagliato = document.getElementById('table-dettagliato');
        const tableSintetico = document.getElementById('table-sintetico');
        const tableGiornaliero = document.getElementById('table-giornaliero');
        const btnDettagliato = document.getElementById('btn-dettagliato');
        const btnSintetico = document.getElementById('btn-sintetico');
        const btnGiornaliero = document.getElementById('btn-giornaliero');
        
        // Nascondi tutte le tabelle
        tableDettagliato.classList.add('hidden');
        tableSintetico.classList.add('hidden');
        tableGiornaliero.classList.add('hidden');
        
        // Reset tutti i pulsanti
        btnDettagliato.classList.remove('btn-primary');
        btnDettagliato.classList.add('btn-outline');
        btnSintetico.classList.remove('btn-primary');
        btnSintetico.classList.add('btn-outline');
        btnGiornaliero.classList.remove('btn-primary');
        btnGiornaliero.classList.add('btn-outline');
        
        // Mostra la tabella selezionata e attiva il pulsante corrispondente
        if (view === 'sintetico') {
            tableSintetico.classList.remove('hidden');
            btnSintetico.classList.remove('btn-outline');
            btnSintetico.classList.add('btn-primary');
            currentActiveView = 'sintetico';
        } else if (view === 'giornaliero') {
            tableGiornaliero.classList.remove('hidden');
            btnGiornaliero.classList.remove('btn-outline');
            btnGiornaliero.classList.add('btn-primary');
            currentActiveView = 'giornaliero';
        } else {
            tableDettagliato.classList.remove('hidden');
            btnDettagliato.classList.remove('btn-outline');
            btnDettagliato.classList.add('btn-primary');
            currentActiveView = 'dettagliato';
        }
        
        // Aggiorna i controlli delle colonne per la nuova vista
        populateColumnControls(view);
    }
    
    // ==========================================
    // POPOLA IL DROPDOWN CON LE COLONNE DELLA VISTA ATTIVA
    // ==========================================
    function populateColumnControls(view) {
        const container = document.getElementById('column-controls-container');
        if (!container) return;
        
        const columns = tableColumns[view] || [];
        container.innerHTML = '';
        
        columns.forEach(column => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'checkbox checkbox-xs';
            checkbox.dataset.column = column.key;
            checkbox.dataset.table = view;
            checkbox.checked = true;
            
            checkbox.addEventListener('change', function() {
                toggleColumnInTable(view, column.key, this.checked);
            });
            
            const span = document.createElement('span');
            span.className = 'text-sm';
            span.textContent = column.label;
            
            label.appendChild(checkbox);
            label.appendChild(span);
            container.appendChild(label);
        });
    }
    
    // ==========================================
    // TOGGLE VISIBILITÀ COLONNA NELLA TABELLA SPECIFICA
    // ==========================================
    function toggleColumnInTable(table, columnKey, isVisible) {
        const tableId = `table-${table}`;
        const displayValue = isVisible ? '' : 'none';
        
        // Nascondi/mostra tutte le celle con classe .col-{columnKey}
        const cells = document.querySelectorAll(`#${tableId} .col-${columnKey}`);
        cells.forEach(cell => {
            cell.style.display = displayValue;
        });
    }
    
    // ==========================================
    // TOGGLE TUTTE LE COLONNE NELLA TABELLA ATTIVA
    // ==========================================
    function toggleAllColumnsInActiveTable(selectAll) {
        const container = document.getElementById('column-controls-container');
        if (!container) return;
        
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll;
            const columnKey = checkbox.dataset.column;
            toggleColumnInTable(currentActiveView, columnKey, selectAll);
        });
    }
    
    // ==========================================
    // DRAG-TO-SCROLL PERSONALIZZATO
    // ==========================================
    function initCustomDragScroll(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.log(`Container ${containerId} non trovato`);
            return;
        }
        
        let isDown = false;
        let startX;
        let scrollLeft;
        
        // Imposta cursore
        container.style.cursor = 'grab';
        
        // Mousedown: inizia il drag
        container.addEventListener('mousedown', (e) => {
            // Ignora link e pulsanti
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                e.target.closest('a') || e.target.closest('button') || 
                e.target.closest('input') || e.target.closest('select')) {
                return;
            }
            
            isDown = true;
            container.style.cursor = 'grabbing';
            container.style.userSelect = 'none';
            
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        
        // Mouseleave: termina il drag
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'grab';
            container.style.userSelect = '';
        });
        
        // Mouseup: termina il drag
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'grab';
            container.style.userSelect = '';
        });
        
        // Mousemove: esegue lo scroll
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 1.5; // Velocità di scroll
            
            container.scrollLeft = scrollLeft - walk;
        });
        
        console.log(`Drag-to-scroll inizializzato per ${containerId}`);
    }
    
    // ==========================================
    // INIZIALIZZAZIONE AL CARICAMENTO PAGINA
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        switchView('sintetico');
        
        // Inizializza drag-to-scroll per tutte le tabelle
        setTimeout(() => {
            initCustomDragScroll('table-dettagliato');
            initCustomDragScroll('table-sintetico');
            initCustomDragScroll('table-giornaliero');
            console.log('Drag-to-scroll inizializzato per tutte le tabelle!');
        }, 100);
    });
</script>

