-- =====================================================
-- MANTENIMENTI BONUS INCENTIVI
-- =====================================================
-- Tabella per gestire i bonus e incentivi per istanze/commesse
-- Gestisce ripartizioni per diverse tipologie (Fissa, Pezzi, Fatturato, ecc.)

CREATE TABLE IF NOT EXISTS `mantenimenti_bonus_incentivi` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `istanza` VARCHAR(255) NULL COMMENT 'Nome istanza (es. NOVA, GT ENERGIE, MEGLIOQUESTO)',
    `commessa` VARCHAR(255) NULL COMMENT 'Nome commessa (es. TIM_CONSUMER, ENI_CONSUMER)',
    `macro_campagna` VARCHAR(255) NULL COMMENT 'Macro campagna di riferimento',
    `tipologia_ripartizione` ENUM('Fissa', 'Pezzi', 'Fatturato', 'Ore', 'ContattiUtili', 'ContattiChiusi') NULL COMMENT 'Tipo di ripartizione bonus',
    `sedi_ripartizione` VARCHAR(500) NULL COMMENT 'Sedi coinvolte nella ripartizione (JSON o CSV)',
    `liste_ripartizione` VARCHAR(500) NULL COMMENT 'Liste coinvolte nella ripartizione (JSON o CSV)',
    `extra_bonus` DECIMAL(10,2) NULL COMMENT 'Importo extra bonus',
    `valido_dal` DATE NULL COMMENT 'Data inizio validità',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indici per performance
    INDEX `idx_istanza` (`istanza`),
    INDEX `idx_commessa` (`commessa`),
    INDEX `idx_macro_campagna` (`macro_campagna`),
    INDEX `idx_tipologia` (`tipologia_ripartizione`),
    INDEX `idx_valido_dal` (`valido_dal`),
    INDEX `idx_istanza_commessa` (`istanza`, `commessa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gestione bonus e incentivi per istanze e commesse';

-- =====================================================
-- QUERY DI TEST
-- =====================================================

-- Verifica tabella creata
-- DESCRIBE mantenimenti_bonus_incentivi;

-- Esempio inserimento
-- INSERT INTO mantenimenti_bonus_incentivi 
-- (istanza, commessa, macro_campagna, tipologia_ripartizione, extra_bonus, valido_dal)
-- VALUES 
-- ('NOVA', 'TIM_CONSUMER', 'TIM_FIBRA_Q4', 'Pezzi', 50.00, '2025-01-01');

-- Verifica dati
-- SELECT * FROM mantenimenti_bonus_incentivi ORDER BY created_at DESC;

