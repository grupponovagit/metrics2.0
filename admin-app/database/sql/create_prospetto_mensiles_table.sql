-- =====================================================
-- CREA TABELLA prospetto_mensiles
-- =====================================================
-- Tabella per gestire i prospetti mensili di marketing
-- con dati JSON per account e settimane
-- =====================================================

USE DB_Metrics;

-- Rimuovi la tabella se esiste (ATTENZIONE: cancella i dati!)
DROP TABLE IF EXISTS prospetto_mensiles;

-- Crea la tabella
CREATE TABLE prospetto_mensiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE COMMENT 'Nome prospetto (es: Novembre 2024)',
    mese INT NOT NULL COMMENT 'Mese (1-12)',
    anno INT NOT NULL COMMENT 'Anno (2020-2100)',
    giorni_lavorativi INT NOT NULL DEFAULT 24 COMMENT 'Giorni lavorativi del mese',
    descrizione TEXT NULL COMMENT 'Descrizione scenario',
    dati_accounts JSON NOT NULL COMMENT 'Dati JSON con accounts e settimane',
    attivo TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Prospetto attivo/inattivo',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    -- Indici per performance
    INDEX idx_mese_anno (mese, anno),
    INDEX idx_attivo (attivo),
    INDEX idx_anno (anno)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Prospetti mensili marketing';

-- Verifica creazione tabella
DESCRIBE prospetto_mensiles;

SELECT 'Tabella prospetto_mensiles creata con successo!' as messaggio;

