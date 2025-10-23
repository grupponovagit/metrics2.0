-- =====================================================
-- CALENDARIO AZIENDALE - Gestione Giorni Lavorativi
-- =====================================================

-- Tabella principale calendario aziendale
CREATE TABLE IF NOT EXISTS `calendario_aziendale` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `data` DATE NOT NULL,
    `anno` INT NOT NULL,
    `mese` INT NOT NULL,
    `giorno` INT NOT NULL,
    `giorno_settimana` INT NOT NULL COMMENT '1=Lunedì, 7=Domenica',
    `tipo_giorno` ENUM('lavorativo', 'festivo', 'sabato', 'domenica', 'eccezione') NOT NULL,
    `peso_giornata` DECIMAL(3,2) NOT NULL DEFAULT 1.00 COMMENT '1.00=giorno intero, 0.50=mezza giornata, 0.00=non lavorativo',
    `descrizione` VARCHAR(255) NULL COMMENT 'Descrizione festa o eccezione',
    `mandato` VARCHAR(100) NULL COMMENT 'Mandato/Fornitore specifico per eccezioni (es. PLENITUDE, TIM)',
    `is_ricorrente` BOOLEAN DEFAULT FALSE COMMENT 'Se true, si ripete ogni anno',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_data` (`data`),
    INDEX `idx_anno_mese` (`anno`, `mese`),
    INDEX `idx_tipo` (`tipo_giorno`),
    INDEX `idx_mandato` (`mandato`),
    UNIQUE KEY `unique_data_mandato` (`data`, `mandato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FESTIVITÀ ITALIANE RICORRENTI
-- =====================================================

-- Funzione per popolare festività dal 2024 al 2030
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS PopolaCalendarioAziendale(IN anno_inizio INT, IN anno_fine INT)
BEGIN
    DECLARE current_anno INT;
    DECLARE current_data DATE;
    DECLARE current_giorno_settimana INT;
    
    SET current_anno = anno_inizio;
    
    WHILE current_anno <= anno_fine DO
        -- Capodanno (1 gennaio)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-01-01'), current_anno, 1, 1, DAYOFWEEK(CONCAT(current_anno, '-01-01')), 'festivo', 0.00, 'Capodanno', TRUE);
        
        -- Epifania (6 gennaio)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-01-06'), current_anno, 1, 6, DAYOFWEEK(CONCAT(current_anno, '-01-06')), 'festivo', 0.00, 'Epifania', TRUE);
        
        -- Festa della Liberazione (25 aprile)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-04-25'), current_anno, 4, 25, DAYOFWEEK(CONCAT(current_anno, '-04-25')), 'festivo', 0.00, 'Festa della Liberazione', TRUE);
        
        -- Festa del Lavoro (1 maggio)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-05-01'), current_anno, 5, 1, DAYOFWEEK(CONCAT(current_anno, '-05-01')), 'festivo', 0.00, 'Festa del Lavoro', TRUE);
        
        -- Festa della Repubblica (2 giugno)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-06-02'), current_anno, 6, 2, DAYOFWEEK(CONCAT(current_anno, '-06-02')), 'festivo', 0.00, 'Festa della Repubblica', TRUE);
        
        -- Ferragosto (15 agosto)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-08-15'), current_anno, 8, 15, DAYOFWEEK(CONCAT(current_anno, '-08-15')), 'festivo', 0.00, 'Ferragosto', TRUE);
        
        -- Ognissanti (1 novembre)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-11-01'), current_anno, 11, 1, DAYOFWEEK(CONCAT(current_anno, '-11-01')), 'festivo', 0.00, 'Ognissanti', TRUE);
        
        -- Immacolata Concezione (8 dicembre)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-12-08'), current_anno, 12, 8, DAYOFWEEK(CONCAT(current_anno, '-12-08')), 'festivo', 0.00, 'Immacolata Concezione', TRUE);
        
        -- Natale (25 dicembre)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-12-25'), current_anno, 12, 25, DAYOFWEEK(CONCAT(current_anno, '-12-25')), 'festivo', 0.00, 'Natale', TRUE);
        
        -- Santo Stefano (26 dicembre)
        INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
        VALUES (CONCAT(current_anno, '-12-26'), current_anno, 12, 26, DAYOFWEEK(CONCAT(current_anno, '-12-26')), 'festivo', 0.00, 'Santo Stefano', TRUE);
        
        -- Popola tutti i giorni dell'anno con regole base
        SET current_data = CONCAT(current_anno, '-01-01');
        WHILE current_data <= CONCAT(current_anno, '-12-31') DO
            SET current_giorno_settimana = DAYOFWEEK(current_data);
            
            -- Inserisci solo se non esiste già (le festività hanno priorità)
            INSERT IGNORE INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, is_ricorrente)
            VALUES (
                current_data,
                YEAR(current_data),
                MONTH(current_data),
                DAY(current_data),
                current_giorno_settimana,
                CASE 
                    WHEN current_giorno_settimana = 1 THEN 'domenica'  -- Domenica
                    WHEN current_giorno_settimana = 7 THEN 'sabato'    -- Sabato
                    ELSE 'lavorativo'
                END,
                CASE 
                    WHEN current_giorno_settimana = 1 THEN 0.00  -- Domenica
                    WHEN current_giorno_settimana = 7 THEN 0.50  -- Sabato (mezza giornata)
                    ELSE 1.00                                     -- Lavorativo
                END,
                CASE 
                    WHEN current_giorno_settimana = 1 THEN 'Domenica'
                    WHEN current_giorno_settimana = 7 THEN 'Sabato'
                    ELSE NULL
                END,
                FALSE
            );
            
            SET current_data = DATE_ADD(current_data, INTERVAL 1 DAY);
        END WHILE;
        
        SET current_anno = current_anno + 1;
    END WHILE;
END$$

DELIMITER ;

-- Popola il calendario dal 2024 al 2030
CALL PopolaCalendarioAziendale(2024, 2030);

-- =====================================================
-- VISTE UTILI PER CALCOLI
-- =====================================================

-- Vista giorni lavorativi per mese
CREATE OR REPLACE VIEW v_giorni_lavorativi_mese AS
SELECT 
    anno,
    mese,
    COUNT(*) as totale_giorni,
    SUM(peso_giornata) as giorni_lavorativi,
    SUM(CASE WHEN tipo_giorno = 'lavorativo' THEN 1 ELSE 0 END) as giorni_pieni,
    SUM(CASE WHEN tipo_giorno = 'sabato' THEN 1 ELSE 0 END) as sabati,
    SUM(CASE WHEN tipo_giorno IN ('festivo', 'domenica') THEN 1 ELSE 0 END) as giorni_non_lavorativi
FROM calendario_aziendale
GROUP BY anno, mese
ORDER BY anno, mese;

-- Vista giorni rimanenti nel mese corrente
CREATE OR REPLACE VIEW v_giorni_rimanenti_mese_corrente AS
SELECT 
    YEAR(CURDATE()) as anno,
    MONTH(CURDATE()) as mese,
    SUM(peso_giornata) as giorni_lavorativi_rimanenti,
    COUNT(*) as giorni_totali_rimanenti
FROM calendario_aziendale
WHERE data >= CURDATE()
  AND YEAR(data) = YEAR(CURDATE())
  AND MONTH(data) = MONTH(CURDATE());

-- =====================================================
-- INDICI AGGIUNTIVI PER PERFORMANCE
-- =====================================================

CREATE INDEX idx_peso ON calendario_aziendale(peso_giornata);
CREATE INDEX idx_ricorrente ON calendario_aziendale(is_ricorrente);

-- =====================================================
-- QUERY DI TEST
-- =====================================================

-- Verifica giorni lavorativi per mese
-- SELECT * FROM v_giorni_lavorativi_mese WHERE anno = 2025;

-- Verifica giorni rimanenti nel mese corrente
-- SELECT * FROM v_giorni_rimanenti_mese_corrente;

-- Festività 2025
-- SELECT data, descrizione, tipo_giorno FROM calendario_aziendale 
-- WHERE anno = 2025 AND tipo_giorno = 'festivo' AND mandato IS NULL ORDER BY data;

-- Eccezioni per mandato specifico
-- SELECT data, descrizione, mandato FROM calendario_aziendale 
-- WHERE mandato = 'PLENITUDE' AND anno = 2025 ORDER BY data;

-- =====================================================
-- ESEMPI ECCEZIONI PER MANDATO
-- =====================================================

-- Esempio: Plenitude blocco manutenzione 15 marzo 2025
-- INSERT INTO calendario_aziendale (data, anno, mese, giorno, giorno_settimana, tipo_giorno, peso_giornata, descrizione, mandato, is_ricorrente)
-- VALUES ('2025-03-15', 2025, 3, 15, DAYOFWEEK('2025-03-15'), 'eccezione', 0.00, 'Manutenzione sistema - Blocco operativo', 'PLENITUDE', FALSE)
-- ON DUPLICATE KEY UPDATE 
--     tipo_giorno = 'eccezione',
--     peso_giornata = 0.00,
--     descrizione = 'Manutenzione sistema - Blocco operativo';


