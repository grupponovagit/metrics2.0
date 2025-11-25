-- =====================================================
-- CREA TABELLA LISTE (se non esiste)
-- =====================================================
-- Tabella per gestire le liste di ripartizione per bonus e incentivi

CREATE TABLE IF NOT EXISTS `liste` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nome_lista` VARCHAR(255) NOT NULL COMMENT 'Nome della lista',
    `descrizione` TEXT NULL COMMENT 'Descrizione della lista',
    `attiva` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Se la lista è attiva o meno',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indici per performance
    INDEX `idx_nome_lista` (`nome_lista`),
    INDEX `idx_attiva` (`attiva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gestione liste per ripartizione bonus';

-- =====================================================
-- INSERIMENTO DATI DI ESEMPIO (opzionale)
-- =====================================================
-- Decommentare se vuoi inserire dati di esempio

-- INSERT INTO `liste` (`nome_lista`, `descrizione`, `attiva`) VALUES
-- ('LISTA_A', 'Lista principale A', 1),
-- ('LISTA_B', 'Lista principale B', 1),
-- ('LISTA_C', 'Lista secondaria C', 1),
-- ('LISTA_VIP', 'Lista clienti VIP', 1),
-- ('LISTA_STANDARD', 'Lista clienti standard', 1);

-- Verifica tabella creata
DESCRIBE liste;


