-- =====================================================
-- AGGIUNGI CAMPO valido_al A mantenimenti_bonus_incentivi
-- =====================================================
-- Aggiunge il campo valido_al per gestire la data di fine validità

ALTER TABLE `mantenimenti_bonus_incentivi` 
ADD COLUMN `valido_al` DATE NULL COMMENT 'Data fine validità' AFTER `valido_dal`;

-- Aggiungi indice per performance
ALTER TABLE `mantenimenti_bonus_incentivi` 
ADD INDEX `idx_valido_al` (`valido_al`);

-- Verifica modifica
DESCRIBE mantenimenti_bonus_incentivi;


