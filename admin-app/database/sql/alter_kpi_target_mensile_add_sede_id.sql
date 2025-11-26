-- Aggiunge campo sede_id alla tabella kpi_target_mensile
-- Data: 2025-11-26
-- Descrizione: Aggiunge relazione con la tabella sedi per identificare univocamente la sede

-- Aggiunge colonna sede_id come foreign key verso sedi
ALTER TABLE `kpi_target_mensile`
ADD COLUMN `sede_id` INT UNSIGNED NULL COMMENT 'ID della sede dalla tabella sedi' AFTER `sede_crm`;

-- Aggiunge indice per ottimizzare le query
ALTER TABLE `kpi_target_mensile`
ADD INDEX `idx_sede_id` (`sede_id`);

-- Aggiunge indice composto per filtri gerarchici completi
ALTER TABLE `kpi_target_mensile`
ADD INDEX `idx_istanza_commessa_macro_sede` (`istanza`, `commessa`, `macro_campagna`, `sede_id`);

-- Aggiunge foreign key constraint (opzionale, commentato per sicurezza)
-- Decommentare solo se si vuole vincolo referenziale stretto
-- ALTER TABLE `kpi_target_mensile`
-- ADD CONSTRAINT `fk_kpi_target_sede`
-- FOREIGN KEY (`sede_id`) REFERENCES `sedi`(`id`)
-- ON DELETE SET NULL
-- ON UPDATE CASCADE;

