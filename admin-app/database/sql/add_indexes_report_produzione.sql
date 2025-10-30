-- =====================================================
-- INDICI PER OTTIMIZZARE REPORT_PRODUZIONE_PIVOT_CACHE
-- =====================================================
-- Questi indici migliorano significativamente le performance
-- delle query nel cruscotto produzione
-- =====================================================

USE DB_Metrics;

-- Rimuovi indici esistenti se ci sono (per evitare errori)
DROP INDEX IF EXISTS idx_filtri_principali ON report_produzione_pivot_cache;
DROP INDEX IF EXISTS idx_data_vendita ON report_produzione_pivot_cache;
DROP INDEX IF EXISTS idx_commessa ON report_produzione_pivot_cache;
DROP INDEX IF EXISTS idx_campagna_commessa ON report_produzione_pivot_cache;
DROP INDEX IF EXISTS idx_sede_commessa ON report_produzione_pivot_cache;

-- Indice composito per i filtri principali (WHERE più comuni)
CREATE INDEX idx_filtri_principali 
ON report_produzione_pivot_cache(
    commessa, 
    data_vendita, 
    nome_sede, 
    campagna_id
);

-- Indice per data_vendita (usato in ORDER BY e WHERE BETWEEN)
CREATE INDEX idx_data_vendita 
ON report_produzione_pivot_cache(data_vendita DESC);

-- Indice per commessa (campo obbligatorio nei filtri)
CREATE INDEX idx_commessa 
ON report_produzione_pivot_cache(commessa);

-- Indice per campagna_id + commessa (per query filtrate)
CREATE INDEX idx_campagna_commessa 
ON report_produzione_pivot_cache(campagna_id, commessa);

-- Indice per nome_sede + commessa (per query filtrate)
CREATE INDEX idx_sede_commessa 
ON report_produzione_pivot_cache(nome_sede, commessa);

-- Verifica indici creati
SHOW INDEX FROM report_produzione_pivot_cache;

-- =====================================================
-- STATISTICHE (opzionale - per il query optimizer)
-- =====================================================
ANALYZE TABLE report_produzione_pivot_cache;

SELECT 
    'Indici creati con successo!' as messaggio,
    COUNT(*) as totale_record,
    COUNT(DISTINCT commessa) as totale_commesse,
    COUNT(DISTINCT nome_sede) as totale_sedi,
    COUNT(DISTINCT campagna_id) as totale_campagne
FROM report_produzione_pivot_cache;

