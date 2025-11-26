-- =====================================================
-- REFACTORING CAMPO sede_id IN mantenimenti_bonus_incentivi
-- =====================================================
-- OBIETTIVO:
-- - sede_id deve contenere id_sede (VARCHAR) invece di id (INT)
-- =====================================================

-- STEP 1: Aggiungi colonna temporanea per il nuovo id_sede
ALTER TABLE mantenimenti_bonus_incentivi 
ADD COLUMN sede_id_new VARCHAR(50) NULL AFTER sede_id;

-- STEP 2: Popola sede_id_new con id_sede dalla tabella sedi
-- Match sull'id numerico attuale
UPDATE mantenimenti_bonus_incentivi mbi
LEFT JOIN sedi s ON mbi.sede_id = s.id
SET mbi.sede_id_new = s.id_sede
WHERE mbi.sede_id IS NOT NULL;

-- STEP 3: Verifica che tutti i record siano stati matchati
-- (Esegui questa query per vedere se ci sono problemi)
-- SELECT sede_id, COUNT(*) as count
-- FROM mantenimenti_bonus_incentivi
-- WHERE sede_id IS NOT NULL AND sede_id_new IS NULL
-- GROUP BY sede_id;

-- STEP 4: Rimuovi la colonna sede_id originale
ALTER TABLE mantenimenti_bonus_incentivi 
DROP COLUMN sede_id;

-- STEP 5: Rinomina sede_id_new in sede_id
ALTER TABLE mantenimenti_bonus_incentivi 
CHANGE COLUMN sede_id_new sede_id VARCHAR(50) NULL;

-- STEP 6: Rimuovi l'indice vecchio (ignora errore se non esiste)
-- Commentato per sicurezza - eseguire manualmente se necessario
-- DROP INDEX idx_sede_id ON mantenimenti_bonus_incentivi;

-- STEP 7: Aggiungi nuovo indice su sede_id
ALTER TABLE mantenimenti_bonus_incentivi 
ADD INDEX idx_sede_id (sede_id);

-- STEP 8: Aggiungi foreign key constraint (opzionale, commentato per sicurezza)
-- ALTER TABLE mantenimenti_bonus_incentivi 
-- ADD CONSTRAINT fk_mantenimenti_sede_id 
-- FOREIGN KEY (sede_id) REFERENCES sedi(id_sede) ON DELETE SET NULL;

-- =====================================================
-- RISULTATO FINALE:
-- - sede_id: VARCHAR(50) (id_sede della tabella sedi)
-- =====================================================

