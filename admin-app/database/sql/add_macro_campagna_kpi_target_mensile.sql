-- Aggiunge colonna macro_campagna alla tabella kpi_target_mensile
-- La colonna servirà per filtrare gli obiettivi per macro campagna

ALTER TABLE `kpi_target_mensile` 
ADD COLUMN `macro_campagna` VARCHAR(100) NOT NULL DEFAULT 'TUTTE' COMMENT 'Macro campagna di riferimento (default: TUTTE)' 
AFTER `sede_estesa`;

-- Aggiorna tutte le righe esistenti con il valore "TUTTE"
UPDATE `kpi_target_mensile` 
SET `macro_campagna` = 'TUTTE' 
WHERE `macro_campagna` IS NULL OR `macro_campagna` = '';

-- Aggiungi indice per performance
ALTER TABLE `kpi_target_mensile` 
ADD INDEX `idx_macro_campagna` (`macro_campagna`);

-- Verifica
SELECT COUNT(*) as totale_righe, macro_campagna 
FROM `kpi_target_mensile` 
GROUP BY macro_campagna;

