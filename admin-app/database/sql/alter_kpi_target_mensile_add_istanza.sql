-- Aggiunge colonna istanza alla tabella kpi_target_mensile
-- Data: 2025-11-26
-- Descrizione: Aggiunge il campo istanza come primo livello di filtro gerarchico

ALTER TABLE `kpi_target_mensile`
ADD COLUMN `istanza` VARCHAR(100) NULL COMMENT 'Istanza di riferimento dalla tabella campagne' AFTER `id`;

-- Aggiunge indice per ottimizzare le query
ALTER TABLE `kpi_target_mensile`
ADD INDEX `idx_istanza` (`istanza`);

-- Aggiunge indice composto per filtri gerarchici
ALTER TABLE `kpi_target_mensile`
ADD INDEX `idx_istanza_commessa_macro` (`istanza`, `commessa`, `macro_campagna`);

