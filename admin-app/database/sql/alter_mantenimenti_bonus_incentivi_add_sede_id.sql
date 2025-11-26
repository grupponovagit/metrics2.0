-- Aggiunge campo sede_id alla tabella mantenimenti_bonus_incentivi
-- Data: 2025-11-26
-- Descrizione: Aggiunge relazione con la tabella sedi per identificare univocamente la sede

-- Aggiunge colonna sede_id come foreign key verso sedi
ALTER TABLE `mantenimenti_bonus_incentivi`
ADD COLUMN `sede_id` INT UNSIGNED NULL COMMENT 'ID della sede dalla tabella sedi' AFTER `sedi_ripartizione`;

-- Aggiunge indice per ottimizzare le query
ALTER TABLE `mantenimenti_bonus_incentivi`
ADD INDEX `idx_sede_id` (`sede_id`);

-- Aggiunge foreign key constraint (opzionale, commentato per sicurezza)
-- Decommentare solo se si vuole vincolo referenziale stretto
-- ALTER TABLE `mantenimenti_bonus_incentivi`
-- ADD CONSTRAINT `fk_mantenimenti_sede`
-- FOREIGN KEY (`sede_id`) REFERENCES `sedi`(`id`)
-- ON DELETE SET NULL
-- ON UPDATE CASCADE;

