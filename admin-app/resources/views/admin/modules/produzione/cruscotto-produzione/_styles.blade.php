{{-- ============================================ --}}
{{-- STYLES PER CRUSCOTTO PRODUZIONE           --}}
{{-- ============================================ --}}

<style>
    /* ========================================== */
    /* DRAG-TO-SCROLL CURSORS                    */
    /* ========================================== */
    #table-dettagliato,
    #table-sintetico,
    #table-giornaliero {
        cursor: grab !important;
    }
    
    #table-dettagliato:active,
    #table-sintetico:active,
    #table-giornaliero:active {
        cursor: grabbing !important;
    }
    
    /* ========================================== */
    /* RIGHE TOTALE STICKY - SINTETICO          */
    /* ========================================== */
    .sticky-totale-table-sintetico {
        position: sticky !important;
        left: 0 !important;
        z-index: 4 !important; /* Z-index più alto delle celle normali */
        width: 450px !important;
        min-width: 450px !important;
        max-width: 450px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        background-color: inherit !important;
    }
    
    /* ========================================== */
    /* RIGHE TOTALE STICKY - DETTAGLIATO        */
    /* ========================================== */
    .sticky-totale-table-dettagliato {
        position: sticky !important;
        left: 0 !important;
        z-index: 4 !important; /* Z-index più alto delle celle normali */
        width: 530px !important;
        min-width: 530px !important;
        max-width: 530px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        background-color: inherit !important;
    }
    
    /* ========================================== */
    /* RIGHE TOTALE STICKY - GIORNALIERO        */
    /* ========================================== */
    .sticky-totale-table-giornaliero {
        position: sticky !important;
        left: 0 !important;
        z-index: 4 !important; /* Z-index più alto delle celle normali */
        width: 270px !important;
        min-width: 270px !important;
        max-width: 270px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.15) !important;
        background-color: inherit !important;
    }
</style>

