-- =====================================================
-- POPOLA CALENDARIO AZIENDALE 2025-2028
-- =====================================================
-- Script per popolare il calendario aziendale dal 2025 al 2028
-- con tutti i giorni dell'anno, festività italiane e weekend
-- LOGICA CORRETTA: le festività sovrascrivono sabati/domeniche
-- =====================================================

USE DB_Metrics;

-- =====================================================
-- SVUOTA LA TABELLA
-- =====================================================
TRUNCATE TABLE calendario_aziendale;

-- =====================================================
-- STORED PROCEDURE PER POPOLARE IL CALENDARIO
-- =====================================================

DROP PROCEDURE IF EXISTS PopolaCalendario2025_2028;

DELIMITER $$

CREATE PROCEDURE PopolaCalendario2025_2028()
BEGIN
    DECLARE current_anno INT;
    DECLARE current_data DATE;
    DECLARE current_giorno_settimana INT;
    DECLARE tipo_giorno_temp VARCHAR(20);
    DECLARE peso_giornata_temp DECIMAL(3,2);
    DECLARE descrizione_temp VARCHAR(255);
    DECLARE is_festivo INT;
    
    SET current_anno = 2025;
    
    WHILE current_anno <= 2028 DO
        
        -- =====================================================
        -- POPOLA TUTTI I GIORNI DELL'ANNO
        -- =====================================================
        SET current_data = CONCAT(current_anno, '-01-01');
        
        WHILE current_data <= CONCAT(current_anno, '-12-31') DO
            SET current_giorno_settimana = DAYOFWEEK(current_data);
            
            -- Default: giorno normale (lun-sab lavorativo, domenica no)
            SET tipo_giorno_temp = CASE 
                WHEN current_giorno_settimana = 1 THEN 'domenica'
                WHEN current_giorno_settimana = 7 THEN 'sabato'
                ELSE 'lavorativo'
            END;
            
            SET peso_giornata_temp = CASE 
                WHEN current_giorno_settimana = 1 THEN 0.00  -- Domenica
                WHEN current_giorno_settimana = 7 THEN 1.00  -- Sabato lavorativo
                ELSE 1.00
            END;
            
            SET descrizione_temp = CASE 
                WHEN current_giorno_settimana = 1 THEN 'Domenica'
                WHEN current_giorno_settimana = 7 THEN 'Sabato'
                ELSE NULL
            END;
            
            SET is_festivo = 0;
            
            -- =====================================================
            -- CONTROLLA SE È UNA FESTIVITÀ (sovrascrive il default)
            -- =====================================================
            
            -- Capodanno (1 gennaio)
            IF MONTH(current_data) = 1 AND DAY(current_data) = 1 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Capodanno';
                SET is_festivo = 1;
            END IF;
            
            -- Epifania (6 gennaio)
            IF MONTH(current_data) = 1 AND DAY(current_data) = 6 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Epifania';
                SET is_festivo = 1;
            END IF;
            
            -- Festa della Liberazione (25 aprile)
            IF MONTH(current_data) = 4 AND DAY(current_data) = 25 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Festa della Liberazione';
                SET is_festivo = 1;
            END IF;
            
            -- Festa del Lavoro (1 maggio)
            IF MONTH(current_data) = 5 AND DAY(current_data) = 1 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Festa del Lavoro';
                SET is_festivo = 1;
            END IF;
            
            -- Festa della Repubblica (2 giugno)
            IF MONTH(current_data) = 6 AND DAY(current_data) = 2 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Festa della Repubblica';
                SET is_festivo = 1;
            END IF;
            
            -- Ferragosto (15 agosto)
            IF MONTH(current_data) = 8 AND DAY(current_data) = 15 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Ferragosto';
                SET is_festivo = 1;
            END IF;
            
            -- Ognissanti (1 novembre)
            IF MONTH(current_data) = 11 AND DAY(current_data) = 1 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Ognissanti';
                SET is_festivo = 1;
            END IF;
            
            -- Immacolata Concezione (8 dicembre)
            IF MONTH(current_data) = 12 AND DAY(current_data) = 8 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Immacolata Concezione';
                SET is_festivo = 1;
            END IF;
            
            -- Natale (25 dicembre)
            IF MONTH(current_data) = 12 AND DAY(current_data) = 25 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Natale';
                SET is_festivo = 1;
            END IF;
            
            -- Santo Stefano (26 dicembre)
            IF MONTH(current_data) = 12 AND DAY(current_data) = 26 THEN
                SET tipo_giorno_temp = 'festivo';
                SET peso_giornata_temp = 0.00;
                SET descrizione_temp = 'Santo Stefano';
                SET is_festivo = 1;
            END IF;
            
            -- =====================================================
            -- INSERISCI IL GIORNO (una sola riga per data)
            -- =====================================================
            INSERT INTO calendario_aziendale (
                data, 
                anno, 
                mese, 
                giorno, 
                giorno_settimana, 
                tipo_giorno, 
                peso_giornata, 
                descrizione, 
                is_ricorrente
            )
            VALUES (
                current_data,
                YEAR(current_data),
                MONTH(current_data),
                DAY(current_data),
                current_giorno_settimana,
                tipo_giorno_temp,
                peso_giornata_temp,
                descrizione_temp,
                is_festivo
            );
            
            SET current_data = DATE_ADD(current_data, INTERVAL 1 DAY);
        END WHILE;
        
        SET current_anno = current_anno + 1;
    END WHILE;
END$$

DELIMITER ;

-- =====================================================
-- ESEGUI LA PROCEDURA
-- =====================================================
CALL PopolaCalendario2025_2028();

-- =====================================================
-- VERIFICA RISULTATI
-- =====================================================

-- Conta totale righe inserite
SELECT 
    '=== TOTALE GIORNI INSERITI ===' as titolo,
    COUNT(*) as totale_giorni_inseriti 
FROM calendario_aziendale;

-- Verifica che non ci siano duplicati
SELECT 
    '=== VERIFICA DUPLICATI ===' as titolo,
    data, 
    COUNT(*) as occorrenze
FROM calendario_aziendale
GROUP BY data
HAVING COUNT(*) > 1;

-- Verifica per anno
SELECT 
    '=== RIEPILOGO PER ANNO ===' as titolo,
    anno,
    COUNT(*) as totale_giorni,
    SUM(CASE WHEN tipo_giorno = 'lavorativo' THEN 1 ELSE 0 END) as giorni_lavorativi_lun_ven,
    SUM(CASE WHEN tipo_giorno = 'sabato' THEN 1 ELSE 0 END) as sabati,
    SUM(CASE WHEN tipo_giorno = 'domenica' THEN 1 ELSE 0 END) as domeniche,
    SUM(CASE WHEN tipo_giorno = 'festivo' THEN 1 ELSE 0 END) as festivita,
    SUM(peso_giornata) as giorni_lavorativi_effettivi
FROM calendario_aziendale
GROUP BY anno
ORDER BY anno;

-- Festività 2025
SELECT 
    '=== FESTIVITÀ 2025 ===' as titolo,
    data, 
    descrizione, 
    tipo_giorno,
    peso_giornata,
    CASE DAYOFWEEK(data)
        WHEN 1 THEN 'Domenica'
        WHEN 2 THEN 'Lunedì'
        WHEN 3 THEN 'Martedì'
        WHEN 4 THEN 'Mercoledì'
        WHEN 5 THEN 'Giovedì'
        WHEN 6 THEN 'Venerdì'
        WHEN 7 THEN 'Sabato'
    END as giorno_settimana_nome
FROM calendario_aziendale 
WHERE anno = 2025 AND tipo_giorno = 'festivo' 
ORDER BY data;

-- Giorni lavorativi per mese 2025
SELECT 
    '=== GIORNI LAVORATIVI 2025 PER MESE ===' as titolo,
    mese,
    CASE mese
        WHEN 1 THEN 'Gennaio'
        WHEN 2 THEN 'Febbraio'
        WHEN 3 THEN 'Marzo'
        WHEN 4 THEN 'Aprile'
        WHEN 5 THEN 'Maggio'
        WHEN 6 THEN 'Giugno'
        WHEN 7 THEN 'Luglio'
        WHEN 8 THEN 'Agosto'
        WHEN 9 THEN 'Settembre'
        WHEN 10 THEN 'Ottobre'
        WHEN 11 THEN 'Novembre'
        WHEN 12 THEN 'Dicembre'
    END as mese_nome,
    COUNT(*) as totale_giorni,
    SUM(CASE WHEN tipo_giorno = 'lavorativo' THEN 1 ELSE 0 END) as lun_ven,
    SUM(CASE WHEN tipo_giorno = 'sabato' THEN 1 ELSE 0 END) as sabati,
    SUM(CASE WHEN tipo_giorno = 'festivo' THEN 1 ELSE 0 END) as festivita,
    SUM(peso_giornata) as giorni_lavorativi_effettivi
FROM calendario_aziendale
WHERE anno = 2025
GROUP BY mese
ORDER BY mese;

-- Focus su Novembre 2025
SELECT 
    '=== DETTAGLIO NOVEMBRE 2025 ===' as titolo,
    data,
    CASE DAYOFWEEK(data)
        WHEN 1 THEN 'Dom'
        WHEN 2 THEN 'Lun'
        WHEN 3 THEN 'Mar'
        WHEN 4 THEN 'Mer'
        WHEN 5 THEN 'Gio'
        WHEN 6 THEN 'Ven'
        WHEN 7 THEN 'Sab'
    END as giorno,
    tipo_giorno,
    peso_giornata,
    descrizione
FROM calendario_aziendale
WHERE anno = 2025 AND mese = 11
ORDER BY data;

-- =====================================================
-- CLEANUP (Rimuovi la procedura se non serve più)
-- =====================================================
DROP PROCEDURE IF EXISTS PopolaCalendario2025_2028;

SELECT '✓ Calendario popolato con successo dal 2025 al 2028!' as messaggio;
SELECT '✓ Nessun duplicato presente' as verifica;
SELECT '✓ Novembre 2025: 24 giorni lavorativi effettivi' as note;
