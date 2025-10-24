-- ============================================
-- AGGIUNTA CAMPI VALIDITA TEMPORALE
-- Tabella: kpi_target_mensile
-- Data: 2025-01-24
-- ============================================

-- Descrizione:
-- Aggiunge i campi per gestire la variazione dei target KPI
-- nel corso del mese corrente con UN SOLO RECORD.

-- ============================================
-- STEP 1: Aggiungi colonne per gestione variazione KPI
-- ============================================

ALTER TABLE kpi_target_mensile 
ADD COLUMN kpi_variato DECIMAL(10,2) NULL COMMENT 'Nuovo valore KPI se cambia nel mese',
ADD COLUMN data_validita_inizio DATE NULL COMMENT 'Data da cui si applica kpi_variato (se NULL, solo valore_kpi)',
ADD COLUMN data_validita_fine DATE NULL COMMENT 'Data fino a cui si applica kpi_variato (NULL = fine mese)';

-- ============================================
-- STEP 2: Crea indice per performance nelle query temporali
-- ============================================

CREATE INDEX idx_kpi_validita ON kpi_target_mensile (
    anno, 
    mese, 
    commessa, 
    nome_kpi,
    data_validita_inizio
);

-- ============================================
-- STEP 3: (OPZIONALE) Aggiungi constraint per validazione
-- ============================================

-- Assicura che se kpi_variato è valorizzato, ci sia anche data_validita_inizio
ALTER TABLE kpi_target_mensile
ADD CONSTRAINT chk_kpi_variato 
CHECK (
    (kpi_variato IS NULL AND data_validita_inizio IS NULL) 
    OR 
    (kpi_variato IS NOT NULL AND data_validita_inizio IS NOT NULL)
);

-- Assicura che data_validita_fine sia sempre >= data_validita_inizio
ALTER TABLE kpi_target_mensile
ADD CONSTRAINT chk_validita_date 
CHECK (
    data_validita_inizio IS NULL 
    OR data_validita_fine IS NULL 
    OR data_validita_fine >= data_validita_inizio
);

-- ============================================
-- VERIFICA RISULTATI
-- ============================================

-- Mostra la struttura aggiornata della tabella
DESCRIBE kpi_target_mensile;

-- Mostra alcuni record di esempio
SELECT 
    id,
    commessa,
    nome_kpi,
    anno,
    mese,
    valore_kpi as valore_iniziale,
    kpi_variato as valore_modificato,
    data_validita_inizio as data_cambio,
    data_validita_fine,
    CASE 
        WHEN kpi_variato IS NOT NULL THEN 'VARIATO'
        ELSE 'ORIGINALE'
    END as stato
FROM kpi_target_mensile 
ORDER BY anno DESC, mese DESC, commessa, nome_kpi
LIMIT 20;

-- ============================================
-- ESEMPI DI UTILIZZO
-- ============================================

-- ESEMPIO 1: Inserire un KPI normale (senza variazioni)
/*
INSERT INTO kpi_target_mensile 
(commessa, nome_kpi, anno, mese, valore_kpi, sede_crm)
VALUES 
('TIM_CONSUMER', 'Vendite', 2024, 1, 100, 'LAMEZIA');

-- Risultato: valore 100 valido per tutto gennaio
*/

-- ESEMPIO 2: Modificare il KPI dal 15 del mese
/*
UPDATE kpi_target_mensile 
SET 
    kpi_variato = 120,
    data_validita_inizio = '2024-01-15',
    data_validita_fine = NULL  -- NULL = fino a fine mese
WHERE commessa = 'TIM_CONSUMER'
  AND nome_kpi = 'Vendite'
  AND anno = 2024
  AND mese = 1;

-- Risultato:
-- Dal 1 al 14 gennaio: usa valore_kpi (100)
-- Dal 15 al 31 gennaio: usa kpi_variato (120)
*/

-- ============================================
-- QUERY UTILI PER RECUPERARE I DATI
-- ============================================

-- 1. Calcolare il valore KPI valido per una data specifica
/*
DELIMITER $$

CREATE FUNCTION get_kpi_valore_per_data(
    p_commessa VARCHAR(100),
    p_nome_kpi VARCHAR(100),
    p_anno INT,
    p_mese INT,
    p_data DATE
) RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE v_valore_kpi DECIMAL(10,2);
    DECLARE v_kpi_variato DECIMAL(10,2);
    DECLARE v_data_inizio DATE;
    
    SELECT 
        valore_kpi,
        kpi_variato,
        data_validita_inizio
    INTO v_valore_kpi, v_kpi_variato, v_data_inizio
    FROM kpi_target_mensile
    WHERE commessa = p_commessa
      AND nome_kpi = p_nome_kpi
      AND anno = p_anno
      AND mese = p_mese
    LIMIT 1;
    
    -- Se c'è un valore variato e la data è >= data_validita_inizio
    IF v_kpi_variato IS NOT NULL AND v_data_inizio IS NOT NULL AND p_data >= v_data_inizio THEN
        RETURN v_kpi_variato;
    ELSE
        RETURN v_valore_kpi;
    END IF;
END$$

DELIMITER ;

-- Utilizzo:
-- SELECT get_kpi_valore_per_data('TIM_CONSUMER', 'Vendite', 2024, 1, '2024-01-20');
*/

-- 2. Query per vedere quale valore si applica in ogni giorno del mese
/*
WITH RECURSIVE giorni AS (
    SELECT DATE('2024-01-01') as giorno
    UNION ALL
    SELECT DATE_ADD(giorno, INTERVAL 1 DAY)
    FROM giorni
    WHERE giorno < LAST_DAY('2024-01-01')
)
SELECT 
    g.giorno,
    CASE 
        WHEN k.kpi_variato IS NOT NULL 
             AND k.data_validita_inizio IS NOT NULL 
             AND g.giorno >= k.data_validita_inizio
             AND (k.data_validita_fine IS NULL OR g.giorno <= k.data_validita_fine)
        THEN k.kpi_variato
        ELSE k.valore_kpi
    END as valore_applicato
FROM giorni g
CROSS JOIN kpi_target_mensile k
WHERE k.commessa = 'TIM_CONSUMER'
  AND k.nome_kpi = 'Vendite'
  AND k.anno = 2024
  AND k.mese = 1
ORDER BY g.giorno;
*/

-- 3. Calcolare la media ponderata del mese
/*
SELECT 
    commessa,
    nome_kpi,
    anno,
    mese,
    valore_kpi as valore_iniziale,
    kpi_variato as valore_modificato,
    CASE 
        WHEN kpi_variato IS NOT NULL AND data_validita_inizio IS NOT NULL THEN
            -- Calcola giorni per valore_kpi (dall'inizio del mese fino al giorno prima del cambio)
            ROUND(
                (
                    (valore_kpi * (DAY(data_validita_inizio) - 1))
                    +
                    (kpi_variato * (DAY(LAST_DAY(CONCAT(anno, '-', LPAD(mese, 2, '0'), '-01'))) - DAY(data_validita_inizio) + 1))
                )
                /
                DAY(LAST_DAY(CONCAT(anno, '-', LPAD(mese, 2, '0'), '-01')))
            , 2)
        ELSE
            valore_kpi  -- Se non è cambiato, il valore è quello originale
    END as valore_medio_ponderato
FROM kpi_target_mensile
WHERE anno = 2024 AND mese = 1;
*/

-- 4. Trova tutti i KPI che sono stati modificati nel mese
/*
SELECT * FROM kpi_target_mensile
WHERE kpi_variato IS NOT NULL
ORDER BY anno DESC, mese DESC;
*/

-- ============================================
-- ESEMPI PRATICI
-- ============================================

-- Scenario: Target Vendite TIM_CONSUMER
-- - Dal 1 al 14 gennaio: 100
-- - Dal 15 al 31 gennaio: 120

-- Inserimento iniziale (1 gennaio)
/*
INSERT INTO kpi_target_mensile 
(commessa, nome_kpi, anno, mese, valore_kpi, sede_crm)
VALUES 
('TIM_CONSUMER', 'Vendite', 2024, 1, 100, 'LAMEZIA');
*/

-- Modifica il 15 gennaio
/*
UPDATE kpi_target_mensile 
SET 
    kpi_variato = 120,
    data_validita_inizio = '2024-01-15'
WHERE commessa = 'TIM_CONSUMER'
  AND nome_kpi = 'Vendite'
  AND anno = 2024
  AND mese = 1;
*/

-- Verifica: quale valore si applica il 10 gennaio?
/*
SELECT 
    CASE 
        WHEN kpi_variato IS NOT NULL 
             AND data_validita_inizio IS NOT NULL 
             AND '2024-01-10' >= data_validita_inizio
        THEN kpi_variato
        ELSE valore_kpi
    END as valore_da_applicare
FROM kpi_target_mensile
WHERE commessa = 'TIM_CONSUMER'
  AND nome_kpi = 'Vendite'
  AND anno = 2024
  AND mese = 1;
-- Risultato: 100
*/

-- Verifica: quale valore si applica il 20 gennaio?
/*
SELECT 
    CASE 
        WHEN kpi_variato IS NOT NULL 
             AND data_validita_inizio IS NOT NULL 
             AND '2024-01-20' >= data_validita_inizio
        THEN kpi_variato
        ELSE valore_kpi
    END as valore_da_applicare
FROM kpi_target_mensile
WHERE commessa = 'TIM_CONSUMER'
  AND nome_kpi = 'Vendite'
  AND anno = 2024
  AND mese = 1;
-- Risultato: 120
*/

-- ============================================
-- ROLLBACK (in caso di necessità)
-- ============================================

-- ATTENZIONE: Esegui solo se vuoi rimuovere completamente le modifiche!
-- Decommenta le righe sotto SOLO se necessario:

/*
ALTER TABLE kpi_target_mensile
DROP INDEX idx_kpi_validita,
DROP CONSTRAINT chk_kpi_variato,
DROP CONSTRAINT chk_validita_date,
DROP COLUMN data_validita_fine,
DROP COLUMN data_validita_inizio,
DROP COLUMN kpi_variato;
*/

-- ============================================
-- FINE SCRIPT
-- ============================================
