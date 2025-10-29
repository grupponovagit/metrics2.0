-- Aggiunta colonna tipo_kpi alla tabella kpi_target_mensile
-- Data creazione: 2025-10-29
-- Descrizione: Colonna per identificare la tipologia di KPI (es: PRODOTTO, INSERITO, RESA, etc.)

ALTER TABLE kpi_target_mensile 
ADD COLUMN tipo_kpi VARCHAR(50) NULL AFTER nome_kpi;

-- Query per verificare i record con tipo_kpi NULL
SELECT 
    id,
    commessa,
    sede_crm,
    macro_campagna,
    nome_kpi,
    tipo_kpi,
    anno,
    mese,
    valore_kpi
FROM kpi_target_mensile 
WHERE tipo_kpi IS NULL
ORDER BY commessa, sede_crm, macro_campagna, nome_kpi;

-- Query per contare i record con tipo_kpi NULL
SELECT COUNT(*) as totale_record_null 
FROM kpi_target_mensile 
WHERE tipo_kpi IS NULL;

