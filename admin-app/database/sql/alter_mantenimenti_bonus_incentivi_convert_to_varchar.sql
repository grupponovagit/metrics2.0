-- Converte campi da JSON a VARCHAR nella tabella mantenimenti_bonus_incentivi
-- Data: 2025-11-26
-- Descrizione: Converte macro_campagna e sedi_ripartizione da selezione multipla a selezione singola

-- Modifica tipo colonna macro_campagna da TEXT/JSON a VARCHAR
ALTER TABLE `mantenimenti_bonus_incentivi`
MODIFY COLUMN `macro_campagna` VARCHAR(255) NULL COMMENT 'Macro campagna singola';

-- Modifica tipo colonna sedi_ripartizione da TEXT/JSON a VARCHAR
ALTER TABLE `mantenimenti_bonus_incentivi`
MODIFY COLUMN `sedi_ripartizione` VARCHAR(255) NULL COMMENT 'Sede di ripartizione singola';

-- Aggiorna eventuali valori JSON esistenti alla prima voce (pulizia dati)
UPDATE `mantenimenti_bonus_incentivi`
SET `macro_campagna` = JSON_UNQUOTE(JSON_EXTRACT(`macro_campagna`, '$[0]'))
WHERE `macro_campagna` IS NOT NULL 
  AND `macro_campagna` LIKE '[%'
  AND JSON_VALID(`macro_campagna`);

UPDATE `mantenimenti_bonus_incentivi`
SET `sedi_ripartizione` = JSON_UNQUOTE(JSON_EXTRACT(`sedi_ripartizione`, '$[0]'))
WHERE `sedi_ripartizione` IS NOT NULL 
  AND `sedi_ripartizione` LIKE '[%'
  AND JSON_VALID(`sedi_ripartizione`);

