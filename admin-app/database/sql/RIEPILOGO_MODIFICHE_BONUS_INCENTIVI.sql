-- =====================================================
-- RIEPILOGO MODIFICHE DATABASE - BONUS E INCENTIVI
-- =====================================================
-- Eseguire queste query in ordine per applicare tutte le modifiche

-- 1. AGGIUNGI CAMPO valido_al
-- =====================================================
ALTER TABLE `mantenimenti_bonus_incentivi` 
ADD COLUMN `valido_al` DATE NULL COMMENT 'Data fine validità' AFTER `valido_dal`;

-- Aggiungi indice per performance
ALTER TABLE `mantenimenti_bonus_incentivi` 
ADD INDEX `idx_valido_al` (`valido_al`);

-- =====================================================
-- 2. VERIFICA MODIFICHE
-- =====================================================
DESCRIBE mantenimenti_bonus_incentivi;

-- Dovresti vedere la colonna valido_al dopo valido_dal

-- =====================================================
-- RIEPILOGO MODIFICHE COMPLETATE
-- =====================================================
-- ✅ Aggiunto campo valido_al
-- ✅ Aggiunto indice per performance
-- ✅ Create/Edit form ora hanno:
--    - Select per Istanza (da report_produzione_pivot_cache)
--    - Select per Commessa (da report_produzione_pivot_cache)
--    - Select per Macro Campagna (da campagne)
--    - Select multipla per Sedi Ripartizione (da sedi)
--    - Textarea per Liste Ripartizione (input testo)
--    - Campo Valido Dal (date)
--    - Campo Valido Al (date) - NUOVO


