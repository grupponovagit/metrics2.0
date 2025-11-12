{{-- ============================================ --}}
{{-- SCRIPTS PER CRUSCOTTO LEAD MARKETING       --}}
{{-- ============================================ --}}

<script>
    // ========================================== 
    // CONFIGURAZIONE COLONNE PER OGNI VISTA
    // ==========================================
    const tableColumns = {
        'sintetico': [
            { key: 'costo', label: 'Costo' },
            { key: 'leads', label: 'Lead' },
            { key: 'click', label: 'Click' },
            { key: 'ore', label: 'Ore' },
            { key: 'ricavi', label: 'Ricavi' },
            { key: 'conversioni', label: 'Conversioni (tutti)' },
            { key: 'economics', label: 'Economics (tutti)' },
            { key: 'performance', label: 'Performance (tutti)' }
        ],
        'giornaliero': [
            { key: 'costo', label: 'Costo' },
            { key: 'leads', label: 'Lead' },
            { key: 'click', label: 'Click' },
            { key: 'ore', label: 'Ore' },
            { key: 'ricavi', label: 'Ricavi' },
            { key: 'conversioni', label: 'Conversioni (tutti)' },
            { key: 'economics', label: 'Economics (tutti)' },
            { key: 'performance', label: 'Performance (tutti)' }
        ]
    };
    
    let currentActiveView = 'sintetico';
    
    // ==========================================
    // SWITCH TRA VISTE (SINTETICO/GIORNALIERO)
    // ==========================================
    function switchView(view) {
        const tableSintetico = document.getElementById('table-sintetico');
        const tableGiornaliero = document.getElementById('table-giornaliero');
        const btnSintetico = document.getElementById('btn-sintetico');
        const btnGiornaliero = document.getElementById('btn-giornaliero');
    
    // Nascondi tutte le tabelle
        tableSintetico.classList.add('hidden');
        tableGiornaliero.classList.add('hidden');
        
        // Reset tutti i pulsanti
        btnSintetico.classList.remove('btn-info');
        btnSintetico.classList.add('btn-outline');
        btnGiornaliero.classList.remove('btn-info');
        btnGiornaliero.classList.add('btn-outline');
        
        // Mostra la tabella selezionata e attiva il pulsante corrispondente
        if (view === 'sintetico') {
            tableSintetico.classList.remove('hidden');
            btnSintetico.classList.remove('btn-outline');
            btnSintetico.classList.add('btn-info');
            currentActiveView = 'sintetico';
        } else if (view === 'giornaliero') {
            tableGiornaliero.classList.remove('hidden');
            btnGiornaliero.classList.remove('btn-outline');
            btnGiornaliero.classList.add('btn-info');
            currentActiveView = 'giornaliero';
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
        const container = document.getElementById(containerId + '-scroll-container');
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
            initCustomDragScroll('table-sintetico');
            initCustomDragScroll('table-giornaliero');
        }, 100);
});
</script>
