@props([
    'id' => 'table-' . uniqid(),
    'title' => '',
    'subtitle' => '',
    'data' => [],
    'columns' => [],
    'stickyColumns' => [],
    'enableDragScroll' => true,
    'enableColumnToggle' => true,
    'maxHeight' => '70vh',
    'showPeriodInfo' => false,
    'periodStart' => null,
    'periodEnd' => null,
    'totalValue' => null,
    'emptyMessage' => 'Nessun dato disponibile',
    'emptySubtitle' => 'Prova a modificare i filtri per visualizzare i dati',
])

@php
    // Genera configurazione colonne per JavaScript
    $columnConfig = collect($columns)->mapWithKeys(function($col) {
        return [$col['key'] => [
            'label' => $col['label'],
            'toggleable' => $col['toggleable'] ?? true,
            'bgColor' => $col['bgColor'] ?? null,
        ]];
    })->toArray();
    
    // Calcola larghezza totale colonne sticky
    $stickyWidth = collect($stickyColumns)->sum('width');
@endphp

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'w-full']) }}>
    {{-- Header tabella con titolo e controlli --}}
    @if($title || $enableColumnToggle || $slot->isNotEmpty())
    <div class="p-6 border-b border-base-300 flex justify-between items-center bg-base-100">
        <div>
            @if($title)
            <h3 class="text-xl font-bold text-base-content">{{ $title }}</h3>
            @endif
            @if($subtitle)
            <p class="text-sm text-base-content/60 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        
        <div class="flex gap-2 items-center">
            {{-- Slot per pulsanti personalizzati (es: Sintetico/Dettagliato/Giornaliero) --}}
            {{ $slot }}
            
            @if($enableColumnToggle && count($columns) > 0)
            {{-- Separatore --}}
            @if($slot->isNotEmpty())
            <div class="divider divider-horizontal mx-2"></div>
            @endif
            
            {{-- Dropdown per gestire colonne visibili --}}
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-sm btn-outline gap-2">
                    <x-ui.icon name="cog" class="h-4 w-4" />
                    Colonne
                </label>
                <div tabindex="0" class="dropdown-content z-[999] menu p-3 shadow-lg bg-base-100 rounded-box w-72 mt-2 border border-base-300">
                    <div class="mb-2 px-2">
                        <p class="font-semibold text-sm mb-2">Colonne Visibili</p>
                        <p class="text-xs text-base-content/60 mb-3">Seleziona le colonne da visualizzare</p>
                    </div>
                    
                    {{-- Checkbox per ogni colonna --}}
                    <div class="space-y-1 max-h-96 overflow-y-auto">
                        @foreach($columns as $column)
                            @if($column['toggleable'] ?? true)
                            <label class="flex items-center gap-2 py-1.5 px-2 cursor-pointer hover:bg-base-200 rounded">
                                <input type="checkbox" 
                                       class="checkbox checkbox-xs column-toggle-{{ $id }}" 
                                       data-column="{{ $column['key'] }}" 
                                       data-table="{{ $id }}"
                                       checked>
                                <span class="text-sm">{{ $column['label'] }}</span>
                            </label>
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="divider my-2"></div>
                    
                    {{-- Pulsanti rapidi --}}
                    <div class="flex gap-2 px-2">
                        <button onclick="toggleAllColumnsForTable('{{ $id }}', true)" class="btn btn-xs btn-success flex-1">
                            Tutte
                        </button>
                        <button onclick="toggleAllColumnsForTable('{{ $id }}', false)" class="btn btn-xs btn-outline btn-success flex-1">
                            Nessuna
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    {{-- Container tabella con scroll --}}
    <div id="{{ $id }}-scroll-container" 
         class="table-scroll-container" 
         style="overflow-x: auto !important; overflow-y: visible !important;"
         data-drag-scroll="{{ $enableDragScroll ? 'true' : 'false' }}">
        
        <table class="table table-zebra w-full" style="min-width: 100%; table-layout: auto;">
            {{-- Slot per thead e tbody personalizzati --}}
            {{ $table }}
        </table>
    </div>
    
    {{-- Hint scroll mobile --}}
    <div class="p-2 bg-base-200/30 border-t border-base-300 text-center lg:hidden">
        <span class="text-xs text-base-content/50">
            ← Scorri orizzontalmente per vedere tutte le colonne →
        </span>
    </div>
    
    {{-- Info periodo (opzionale) --}}
    @if($showPeriodInfo)
    <div class="p-4 bg-base-200/50 border-t border-base-300">
        <div class="flex items-center justify-between text-sm text-base-content/70">
            <div>
                @if($periodStart && $periodEnd)
                    <span>Periodo: <strong>{{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }}</strong> - <strong>{{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }}</strong></span>
                @else
                    <span>Periodo: <strong>Tutto</strong></span>
                @endif
            </div>
            @if($totalValue !== null)
            <div>
                <span>Totale vendite: <strong>{{ number_format($totalValue) }}</strong></span>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Stili CSS per la tabella --}}
<style>
    /* Container scroll con ombra sulle colonne sticky */
    #{{ $id }} {
        position: relative;
        width: 100%;
    }

    /* Tabella con layout auto */
    #{{ $id }} table {
        table-layout: auto !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    @foreach($stickyColumns as $index => $stickyCol)
    @php
        $leftOffset = collect($stickyColumns)->take($index)->sum('width');
        $colClass = 'sticky-' . $id . '-' . $stickyCol['key'];
    @endphp
    
    /* Colonna sticky: {{ $stickyCol['label'] }} */
    #{{ $id }} .{{ $colClass }} {
        position: sticky !important;
        left: {{ $leftOffset }}px !important;
        z-index: 3 !important;
        background-color: white !important;
        width: {{ $stickyCol['width'] }}px !important;
        min-width: {{ $stickyCol['width'] }}px !important;
        max-width: {{ $stickyCol['width'] }}px !important;
        white-space: {{ $stickyCol['wrap'] ?? 'normal' }} !important;
        word-wrap: {{ $stickyCol['wrap'] === 'nowrap' ? 'normal' : 'break-word' }} !important;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        padding: 12px 16px !important;
    }
    
    /* Header sticky con z-index maggiore */
    #{{ $id }} thead th.{{ $colClass }} {
        z-index: 15 !important;
        background-color: #f3f4f6 !important;
    }
    
    /* Mantieni background su righe alternate */
    #{{ $id }} tbody tr:nth-child(odd) td.{{ $colClass }} {
        background-color: white !important;
    }
    
    #{{ $id }} tbody tr:nth-child(even) td.{{ $colClass }} {
        background-color: #f9fafb !important;
    }
    
    /* Background per righe totale */
    #{{ $id }} tbody tr.bg-slate-100 td.{{ $colClass }},
    #{{ $id }} tbody tr.bg-slate-100 th.{{ $colClass }} {
        background-color: #f1f5f9 !important;
    }
    
    #{{ $id }} tbody tr.bg-slate-200 td.{{ $colClass }},
    #{{ $id }} tbody tr.bg-slate-200 th.{{ $colClass }} {
        background-color: #e2e8f0 !important;
    }
    @endforeach
    
    /* ===== CELLE STICKY - Testo visibile in LIGHT MODE ===== */
    #{{ $id }} .sticky-table-dettagliato-cliente,
    #{{ $id }} .sticky-table-dettagliato-sede,
    #{{ $id }} .sticky-table-dettagliato-campagna,
    #{{ $id }} .sticky-table-sintetico-cliente,
    #{{ $id }} .sticky-table-sintetico-sede,
    #{{ $id }} .sticky-table-giornaliero-data,
    #{{ $id }} .sticky-table-giornaliero-cliente {
        color: #1f2937 !important; /* gray-800 - testo scuro per light mode */
    }
    
    @if(count($stickyColumns) > 0)
    /* Celle totale sticky (colspan = numero colonne sticky) */
    #{{ $id }} .sticky-totale-{{ $id }} {
        position: sticky !important;
        left: 0 !important;
        z-index: 3 !important;
        width: {{ $stickyWidth }}px !important;
        min-width: {{ $stickyWidth }}px !important;
        max-width: {{ $stickyWidth }}px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
    }
    @endif
    
    /* Header sticky per scroll verticale */
    #{{ $id }} thead th {
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        background-color: #f3f4f6 !important;
    }
    
    /* ===== MIGLIORAMENTI VISIVI PER DISTINGUERE LE RIGHE ===== */
    
    /* Bordi per header */
    #{{ $id }} thead th {
        border-bottom: 3px solid #94a3b8 !important;
        vertical-align: middle !important;
        padding: 14px 12px !important;
        font-weight: 700 !important;
    }
    
    #{{ $id }} thead tr:first-child th {
        border-bottom: 2px solid #94a3b8 !important;
    }
    
    #{{ $id }} thead tr:last-child th {
        border-bottom: 3px solid #64748b !important;
    }
    
    /* Bordi orizzontali tra le righe */
    #{{ $id }} tbody tr {
        border-bottom: 2px solid #d1d5db !important;
    }
    
    #{{ $id }} tbody tr td {
        border-bottom: 1px solid #d1d5db !important;
        vertical-align: middle !important;
        padding: 14px 12px !important;
    }
    
    /* Contrasto maggiore per righe alternate */
    #{{ $id }} tbody tr:nth-child(odd) {
        background-color: #ffffff !important;
    }
    
    #{{ $id }} tbody tr:nth-child(even) {
        background-color: #f8fafc !important;
    }
    
    /* Righe totale con bordi più evidenti */
    #{{ $id }} tbody tr.bg-slate-100 {
        border-top: 3px solid #94a3b8 !important;
        border-bottom: 3px solid #94a3b8 !important;
        background-color: #f1f5f9 !important;
    }
    
    #{{ $id }} tbody tr.bg-slate-200 {
        border-top: 4px solid #64748b !important;
        border-bottom: 4px solid #64748b !important;
        background-color: #e2e8f0 !important;
    }
    
    /* Hover su righe totale */
    #{{ $id }} tbody tr.bg-slate-100:hover {
        background-color: #e0e7ef !important;
    }
    
    #{{ $id }} tbody tr.bg-slate-200:hover {
        background-color: #cbd5e1 !important;
    }
    
    /* ========================================= */
    /* DARK MODE SUPPORT */
    /* ========================================= */
    @media (prefers-color-scheme: dark) {
        /* Header tabelle - Dark mode */
        [data-theme="dark"] #{{ $id }} thead,
        [data-theme="dark"] #{{ $id }} thead th,
        html.dark #{{ $id }} thead,
        html.dark #{{ $id }} thead th {
            background-color: #1f2937 !important; /* gray-800 */
            color: #f9fafb !important; /* gray-50 */
            border-color: #374151 !important; /* gray-700 */
        }
        
        /* IMPORTANTE: Tutte le celle hanno background blu scuro */
        [data-theme="dark"] #{{ $id }} tbody td,
        html.dark #{{ $id }} tbody td {
            background-color: #1e293b !important; /* slate-800 - blu scuro come nello screenshot */
            color: #f1f5f9 !important; /* slate-100 - testo chiaro */
        }
        
        /* Righe alternate - stesso colore */
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td,
        html.dark #{{ $id }} tbody tr:nth-child(even) td {
            background-color: #1e293b !important; /* slate-800 */
            color: #f1f5f9 !important; /* slate-100 */
        }
        
        /* Testi nelle celle sticky laterali = chiari */
        [data-theme="dark"] #{{ $id }} .sticky-table-dettagliato-cliente,
        [data-theme="dark"] #{{ $id }} .sticky-table-dettagliato-sede,
        [data-theme="dark"] #{{ $id }} .sticky-table-dettagliato-campagna,
        [data-theme="dark"] #{{ $id }} .sticky-table-sintetico-cliente,
        [data-theme="dark"] #{{ $id }} .sticky-table-sintetico-sede,
        [data-theme="dark"] #{{ $id }} .sticky-table-sintetico-ragione_sociale,
        [data-theme="dark"] #{{ $id }} .sticky-table-sintetico-provenienza,
        [data-theme="dark"] #{{ $id }} .sticky-table-giornaliero-data,
        [data-theme="dark"] #{{ $id }} .sticky-table-giornaliero-cliente,
        [data-theme="dark"] #{{ $id }} .sticky-table-giornaliero-campagna,
        html.dark #{{ $id }} .sticky-table-dettagliato-cliente,
        html.dark #{{ $id }} .sticky-table-dettagliato-sede,
        html.dark #{{ $id }} .sticky-table-dettagliato-campagna,
        html.dark #{{ $id }} .sticky-table-sintetico-cliente,
        html.dark #{{ $id }} .sticky-table-sintetico-sede,
        html.dark #{{ $id }} .sticky-table-sintetico-ragione_sociale,
        html.dark #{{ $id }} .sticky-table-sintetico-provenienza,
        html.dark #{{ $id }} .sticky-table-giornaliero-data,
        html.dark #{{ $id }} .sticky-table-giornaliero-cliente,
        html.dark #{{ $id }} .sticky-table-giornaliero-campagna {
            color: #e5e7eb !important; /* gray-200 */
            background-color: #1f2937 !important; /* gray-800 - come header */
        }
        
        /* Anche per i TD sticky (non solo TH) - Con specificità maggiore per sovrascrivere righe alternate */
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-dettagliato-cliente,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-dettagliato-cliente,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-dettagliato-sede,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-dettagliato-sede,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-dettagliato-campagna,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-dettagliato-campagna,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-cliente,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-cliente,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-sede,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-sede,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-ragione_sociale,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-ragione_sociale,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-provenienza,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-provenienza,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-giornaliero-data,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-giornaliero-data,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-giornaliero-cliente,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-giornaliero-cliente,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-giornaliero-campagna,
        [data-theme="dark"] #{{ $id }} tbody tr:nth-child(even) td.sticky-table-giornaliero-campagna,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-dettagliato-cliente,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-dettagliato-cliente,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-dettagliato-sede,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-dettagliato-sede,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-dettagliato-campagna,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-dettagliato-campagna,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-cliente,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-cliente,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-sede,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-sede,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-ragione_sociale,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-ragione_sociale,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-sintetico-provenienza,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-sintetico-provenienza,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-giornaliero-data,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-giornaliero-data,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-giornaliero-cliente,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-giornaliero-cliente,
        html.dark #{{ $id }} tbody tr:nth-child(odd) td.sticky-table-giornaliero-campagna,
        html.dark #{{ $id }} tbody tr:nth-child(even) td.sticky-table-giornaliero-campagna {
            background-color: #1f2937 !important; /* gray-800 */
            color: #e5e7eb !important; /* gray-200 */
        }
        
        /* Background per celle sticky con classi specifiche */
        [data-theme="dark"] #{{ $id }} .bg-base-200\/30,
        html.dark #{{ $id }} .bg-base-200\/30 {
            background-color: #1f2937 !important; /* gray-800 */
        }
        
        [data-theme="dark"] #{{ $id }} .bg-base-100,
        html.dark #{{ $id }} .bg-base-100 {
            background-color: #1f2937 !important; /* gray-800 */
        }
        
        [data-theme="dark"] #{{ $id }} .bg-base-50,
        html.dark #{{ $id }} .bg-base-50 {
            background-color: #1f2937 !important; /* gray-800 */
        }
        
        /* Righe totale - Dark mode */
        [data-theme="dark"] #{{ $id }} .bg-slate-100,
        html.dark #{{ $id }} .bg-slate-100 {
            background-color: #334155 !important; /* slate-700 */
            border-color: #475569 !important; /* slate-600 */
            color: #f9fafb !important; /* Testo chiaro per totali */
        }
        
        [data-theme="dark"] #{{ $id }} .bg-slate-200,
        html.dark #{{ $id }} .bg-slate-200 {
            background-color: #475569 !important; /* slate-600 */
            border-color: #64748b !important; /* slate-500 */
            color: #f9fafb !important; /* Testo chiaro per totali */
        }
        
        /* Celle totale sticky */
        [data-theme="dark"] #{{ $id }} .sticky-totale-table-sintetico,
        [data-theme="dark"] #{{ $id }} .sticky-totale-table-dettagliato,
        [data-theme="dark"] #{{ $id }} .sticky-totale-table-giornaliero,
        html.dark #{{ $id }} .sticky-totale-table-sintetico,
        html.dark #{{ $id }} .sticky-totale-table-dettagliato,
        html.dark #{{ $id }} .sticky-totale-table-giornaliero {
            color: #f9fafb !important; /* Testo chiaro per totali sticky */
        }
        
        /* Bordi - Dark mode */
        [data-theme="dark"] #{{ $id }} .border-base-300,
        [data-theme="dark"] #{{ $id }} .border-slate-300,
        html.dark #{{ $id }} .border-base-300,
        html.dark #{{ $id }} .border-slate-300 {
            border-color: #374151 !important; /* gray-700 */
        }
        
        [data-theme="dark"] #{{ $id }} .border-slate-400,
        html.dark #{{ $id }} .border-slate-400 {
            border-color: #475569 !important; /* slate-600 */
        }
        
        /* Colori differenziati per testi positivi/negativi */
        [data-theme="dark"] #{{ $id }} .text-green-600,
        [data-theme="dark"] #{{ $id }} .text-green-700,
        html.dark #{{ $id }} .text-green-600,
        html.dark #{{ $id }} .text-green-700 {
            color: #16a34a !important; /* green-600 */
        }
        
        [data-theme="dark"] #{{ $id }} .text-red-600,
        [data-theme="dark"] #{{ $id }} .text-red-700,
        html.dark #{{ $id }} .text-red-600,
        html.dark #{{ $id }} .text-red-700 {
            color: #dc2626 !important; /* red-600 */
        }
    }
</style>

{{-- JavaScript per funzionalità tabella --}}
@once
@push('scripts')
<script>
    // Oggetto globale per gestire tutte le tabelle advanced
    window.AdvancedTables = window.AdvancedTables || {
        instances: {},
        
        // Inizializza una tabella
        init: function(tableId, config) {
            this.instances[tableId] = {
                config: config,
                dragScroll: config.enableDragScroll,
                columnToggle: config.enableColumnToggle,
                columns: config.columns
            };
            
            if (config.enableDragScroll) {
                this.initDragScroll(tableId);
            }
            
            if (config.enableColumnToggle) {
                this.initColumnToggle(tableId);
            }
        },
        
        // Inizializza drag-to-scroll
        initDragScroll: function(tableId) {
            const scrollContainer = document.getElementById(tableId + '-scroll-container');
            if (!scrollContainer) return;
            
            let isDown = false;
            let startX;
            let scrollLeft;
            
            scrollContainer.style.cursor = 'grab';
            
            scrollContainer.addEventListener('mousedown', (e) => {
                // Ignora drag su elementi interattivi
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                    e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' ||
                    e.target.closest('a') || e.target.closest('button') || 
                    e.target.closest('input') || e.target.closest('select')) {
                    return;
                }
                
                isDown = true;
                scrollContainer.style.cursor = 'grabbing';
                scrollContainer.style.userSelect = 'none';
                
                startX = e.pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });
            
            scrollContainer.addEventListener('mouseleave', () => {
                isDown = false;
                scrollContainer.style.cursor = 'grab';
                scrollContainer.style.userSelect = '';
            });
            
            scrollContainer.addEventListener('mouseup', () => {
                isDown = false;
                scrollContainer.style.cursor = 'grab';
                scrollContainer.style.userSelect = '';
            });
            
            scrollContainer.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                
                const x = e.pageX - scrollContainer.offsetLeft;
                const walkX = (x - startX) * 2; // Aumentato da 1.5 a 2 per scroll più fluido
                
                scrollContainer.scrollLeft = scrollLeft - walkX;
            });
        },
        
        // Inizializza gestione colonne
        initColumnToggle: function(tableId) {
            const checkboxes = document.querySelectorAll(`.column-toggle-${tableId}`);
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const columnName = this.dataset.column;
                    const isVisible = this.checked;
                    
                    window.AdvancedTables.toggleColumn(tableId, columnName, isVisible);
                });
            });
        },
        
        // Toggle visibilità colonna
        toggleColumn: function(tableId, columnName, isVisible) {
            const displayValue = isVisible ? '' : 'none';
            
            // Usa le classi col-* per nascondere/mostrare
            const cells = document.querySelectorAll(`#${tableId} .col-${columnName}`);
            cells.forEach(cell => {
                cell.style.display = displayValue;
            });
        }
    };
    
    // Funzione helper per toggle tutte le colonne di una specifica tabella
    window.toggleAllColumnsForTable = function(tableId, selectAll) {
        const checkboxes = document.querySelectorAll(`.column-toggle-${tableId}`);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll;
            const columnName = checkbox.dataset.column;
            window.AdvancedTables.toggleColumn(tableId, columnName, selectAll);
        });
    };
</script>
@endpush
@endonce

{{-- Inizializza questa istanza --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.AdvancedTables.init('{{ $id }}', {
            enableDragScroll: {{ $enableDragScroll ? 'true' : 'false' }},
            enableColumnToggle: {{ $enableColumnToggle ? 'true' : 'false' }},
            columns: @json($columnConfig)
        });
    });
</script>
@endpush

