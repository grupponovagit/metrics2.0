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
            { key: 'economics', label: 'Economics (tutti)', subColumns: ['fatturato', 'ricavo_orario'] },
            { key: 'resa', label: 'Resa (tutti)', subColumns: ['resa_prodotto', 'resa_inserito', 'resa_oraria'] },
            { key: 'obiettivi', label: 'Obiettivi (tutti)', subColumns: ['obiettivi-mensile', 'obiettivi-passo', 'obiettivi-diff'] },
            { key: 'paf-mensile', label: 'PAF Mensile (tutti)', subColumns: ['paf-ore', 'paf-pezzi', 'paf-resa', 'paf-fatturato'] }
        ],
        'sintetico': [
            { key: 'prodotto', label: 'Prodotto' },
            { key: 'inserito', label: 'Inserito' },
            { key: 'ko', label: 'KO' },
            { key: 'backlog', label: 'BackLog' },
            { key: 'backlog_partner', label: 'BackLog Partner' },
            { key: 'ore', label: 'Ore' },
            { key: 'economics', label: 'Economics (tutti)', subColumns: ['fatturato', 'ricavo_orario', 'fatturato_paf'] },
            { key: 'resa', label: 'Resa (tutti)', subColumns: ['resa_prodotto', 'resa_inserito', 'resa_oraria'] },
            { key: 'obiettivi', label: 'Obiettivi (tutti)', subColumns: ['obiettivi-mensile', 'obiettivi-passo', 'obiettivi-diff'] },
            { key: 'paf-mensile', label: 'PAF Mensile (tutti)', subColumns: ['paf-ore', 'paf-pezzi', 'paf-resa', 'paf-fatturato'] }
        ],
        'giornaliero': [
            { key: 'prodotto', label: 'Prodotto' },
            { key: 'inserito', label: 'Inserito' },
            { key: 'ko', label: 'KO' },
            { key: 'backlog', label: 'BackLog' },
            { key: 'backlog_partner', label: 'BackLog Partner' },
            { key: 'ore', label: 'Ore' },
            { key: 'economics', label: 'Economics (tutti)', subColumns: ['fatturato', 'ricavo_orario'] },
            { key: 'resa', label: 'Resa (tutti)', subColumns: ['resa_prodotto', 'resa_inserito', 'resa_oraria'] }
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
        const tableGrafico = document.getElementById('table-grafico');
        const btnDettagliato = document.getElementById('btn-dettagliato');
        const btnSintetico = document.getElementById('btn-sintetico');
        const btnGiornaliero = document.getElementById('btn-giornaliero');
        const btnGrafico = document.getElementById('btn-grafico');
        
        // Nascondi tutte le tabelle e viste
        tableDettagliato.classList.add('hidden');
        tableSintetico.classList.add('hidden');
        tableGiornaliero.classList.add('hidden');
        tableGrafico.classList.add('hidden');
        
        // Reset tutti i pulsanti
        btnDettagliato.classList.remove('btn-primary');
        btnDettagliato.classList.add('btn-outline');
        btnSintetico.classList.remove('btn-primary');
        btnSintetico.classList.add('btn-outline');
        btnGiornaliero.classList.remove('btn-primary');
        btnGiornaliero.classList.add('btn-outline');
        btnGrafico.classList.remove('btn-primary');
        btnGrafico.classList.add('btn-outline');
        
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
        } else if (view === 'grafico') {
            tableGrafico.classList.remove('hidden');
            btnGrafico.classList.remove('btn-outline');
            btnGrafico.classList.add('btn-primary');
            currentActiveView = 'grafico';
            
            // Inizializza il grafico se non è già stato fatto
            if (typeof initChart === 'function') {
                setTimeout(() => initChart(), 100);
            }
        } else {
            tableDettagliato.classList.remove('hidden');
            btnDettagliato.classList.remove('btn-outline');
            btnDettagliato.classList.add('btn-primary');
            currentActiveView = 'dettagliato';
        }
        
        // Aggiorna i controlli delle colonne per la nuova vista (solo per tabelle, non per grafico)
        const columnDropdown = document.querySelector('.dropdown.dropdown-end');
        if (view !== 'grafico') {
            if (columnDropdown) columnDropdown.classList.remove('hidden');
        populateColumnControls(view);
        } else {
            if (columnDropdown) columnDropdown.classList.add('hidden');
        }
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
        
        // Trova la configurazione della colonna
        const columnConfig = tableColumns[table].find(col => col.key === columnKey);
        
        // Se la colonna ha sottocolonne, nascondi/mostra tutte le sottocolonne
        if (columnConfig && columnConfig.subColumns) {
            columnConfig.subColumns.forEach(subCol => {
                const cells = document.querySelectorAll(`#${tableId} .col-${columnKey}.col-${subCol}, #${tableId} .col-${columnKey} .col-${subCol}`);
                cells.forEach(cell => {
                    cell.style.display = displayValue;
                });
            });
            
            // Nascondi/mostra anche l'intestazione principale della macro colonna
            const headers = document.querySelectorAll(`#${tableId} th.col-${columnKey}`);
            headers.forEach(header => {
                header.style.display = displayValue;
            });
        } else {
            // Nascondi/mostra tutte le celle con classe .col-{columnKey}
            const cells = document.querySelectorAll(`#${tableId} .col-${columnKey}`);
            cells.forEach(cell => {
                cell.style.display = displayValue;
            });
        }
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
        }, 100);
    });
</script>

