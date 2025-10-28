-- Tabella per la conversione degli esiti specifici in esiti globali
-- Permette di mappare gli esiti specifici di ogni committente (es. Plenitude, Enel)
-- in esiti standard per i calcoli KPI

DROP TABLE IF EXISTS `esiti_conversione`;

CREATE TABLE `esiti_conversione` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `commessa` VARCHAR(100) NOT NULL COMMENT 'Nome della commessa (es: ENI_CONSUMER, TIM_BUSINESS)',
  `esito_originale` VARCHAR(255) NOT NULL COMMENT 'Esito specifico del committente',
  `esito_globale` ENUM('OK', 'KO', 'IN_ATTESA', 'BACKLOG', 'BACKLOG_PARTNER') NOT NULL COMMENT 'Esito standardizzato per calcoli KPI',
  `note` TEXT NULL COMMENT 'Note aggiuntive sulla conversione',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_conversione` (`commessa`, `esito_originale`),
  INDEX `idx_commessa` (`commessa`),
  INDEX `idx_esito_globale` (`esito_globale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversione esiti specifici committenti in esiti globali';

-- Popolamento iniziale con esempi comuni per ENI CONSUMER
INSERT INTO `esiti_conversione` (`commessa`, `esito_originale`, `esito_globale`, `note`) VALUES
-- ENI CONSUMER - Esiti OK
('ENI_CONSUMER', 'ATTIVATA', 'OK', 'Pratica attivata con successo'),
('ENI_CONSUMER', 'ATTIVO', 'OK', 'Contratto attivo'),
('ENI_CONSUMER', 'CONFERMATA', 'OK', 'Pratica confermata dal cliente'),
('ENI_CONSUMER', 'VENDITA OK', 'OK', 'Vendita andata a buon fine'),
('ENI_CONSUMER', 'COMPLETATA', 'OK', 'Pratica completata'),

-- ENI CONSUMER - Esiti KO
('ENI_CONSUMER', 'ANNULLATA', 'KO', 'Pratica annullata'),
('ENI_CONSUMER', 'RESPINTA', 'KO', 'Pratica respinta'),
('ENI_CONSUMER', 'KO TECNICO', 'KO', 'Problemi tecnici'),
('ENI_CONSUMER', 'NON CONFORME', 'KO', 'Non conforme ai requisiti'),
('ENI_CONSUMER', 'RECESSO', 'KO', 'Cliente ha esercitato il recesso'),

-- ENI CONSUMER - In Attesa
('ENI_CONSUMER', 'IN LAVORAZIONE', 'IN_ATTESA', 'Pratica in elaborazione'),
('ENI_CONSUMER', 'ATTESA DOCUMENTI', 'IN_ATTESA', 'In attesa di documentazione'),
('ENI_CONSUMER', 'DA VERIFICARE', 'IN_ATTESA', 'Da verificare'),
('ENI_CONSUMER', 'PENDENTE', 'IN_ATTESA', 'Pratica pendente'),

-- ENI CONSUMER - BackLog
('ENI_CONSUMER', 'IN ATTESA ATTIVAZIONE', 'BACKLOG', 'In coda per attivazione'),
('ENI_CONSUMER', 'DA PROCESSARE', 'BACKLOG', 'Da processare'),
('ENI_CONSUMER', 'SOSPESA', 'BACKLOG', 'Pratica sospesa'),

-- ENI CONSUMER - BackLog Partner
('ENI_CONSUMER', 'PRESSO PARTNER', 'BACKLOG_PARTNER', 'In lavorazione presso partner'),
('ENI_CONSUMER', 'ATTESA PARTNER', 'BACKLOG_PARTNER', 'In attesa risposta partner'),

-- TIM CONSUMER - Esiti OK
('TIM_CONSUMER', 'ATTIVATA', 'OK', 'Linea attivata'),
('TIM_CONSUMER', 'MIGRATA', 'OK', 'Migrazione completata'),
('TIM_CONSUMER', 'OK', 'OK', 'Pratica OK'),
('TIM_CONSUMER', 'VENDITA ANDATA A BUON FINE', 'OK', 'Vendita confermata'),

-- TIM CONSUMER - Esiti KO
('TIM_CONSUMER', 'KO', 'KO', 'Pratica KO'),
('TIM_CONSUMER', 'ANNULLATO', 'KO', 'Ordine annullato'),
('TIM_CONSUMER', 'RIPUDIO', 'KO', 'Cliente ha ripudiato'),
('TIM_CONSUMER', 'NON FATTIBILE', 'KO', 'Tecnicamente non fattibile'),

-- TIM CONSUMER - In Attesa
('TIM_CONSUMER', 'IN LAVORAZIONE', 'IN_ATTESA', 'Ordine in lavorazione'),
('TIM_CONSUMER', 'ATTESA CHIAMATA', 'IN_ATTESA', 'In attesa chiamata cliente'),

-- TIM CONSUMER - BackLog
('TIM_CONSUMER', 'DA ATTIVARE', 'BACKLOG', 'In coda attivazione'),
('TIM_CONSUMER', 'PIANIFICATA', 'BACKLOG', 'Attivazione pianificata'),

-- TIM CONSUMER - BackLog Partner
('TIM_CONSUMER', 'OPEN FIBER', 'BACKLOG_PARTNER', 'In lavorazione Open Fiber'),
('TIM_CONSUMER', 'PRESSO WHOLESALER', 'BACKLOG_PARTNER', 'Presso operatore wholesale'),

-- TIM BUSINESS - Esiti OK
('TIM_BUSINESS', 'ATTIVATA', 'OK', 'Servizio attivato'),
('TIM_BUSINESS', 'CONTRATTO FIRMATO', 'OK', 'Contratto firmato e attivo'),
('TIM_BUSINESS', 'OK', 'OK', 'Pratica OK'),

-- TIM BUSINESS - Esiti KO
('TIM_BUSINESS', 'KO', 'KO', 'Pratica KO'),
('TIM_BUSINESS', 'ANNULLATO', 'KO', 'Contratto annullato'),
('TIM_BUSINESS', 'NON IDONEO', 'KO', 'Cliente non idoneo'),

-- TIM BUSINESS - In Attesa
('TIM_BUSINESS', 'IN VERIFICA', 'IN_ATTESA', 'In fase di verifica'),
('TIM_BUSINESS', 'ATTESA FIRMA', 'IN_ATTESA', 'In attesa firma contratto'),

-- TIM BUSINESS - BackLog
('TIM_BUSINESS', 'IN CODA', 'BACKLOG', 'In coda per attivazione'),

-- TIM BUSINESS - BackLog Partner
('TIM_BUSINESS', 'PRESSO PARTNER', 'BACKLOG_PARTNER', 'Lavorazione partner'),

-- PLENITUDE - Esiti OK
('PLENITUDE', 'ATTIVATA', 'OK', 'Fornitura attivata'),
('PLENITUDE', 'CONTRATTO ATTIVO', 'OK', 'Contratto attivo'),
('PLENITUDE', 'CONFERMATA', 'OK', 'Vendita confermata'),

-- PLENITUDE - Esiti KO
('PLENITUDE', 'KO', 'KO', 'Pratica KO'),
('PLENITUDE', 'RESPINTA', 'KO', 'Pratica respinta'),
('PLENITUDE', 'ANNULLATA', 'KO', 'Vendita annullata'),

-- PLENITUDE - In Attesa
('PLENITUDE', 'IN LAVORAZIONE', 'IN_ATTESA', 'In elaborazione'),
('PLENITUDE', 'DA VERIFICARE', 'IN_ATTESA', 'Da verificare'),

-- PLENITUDE - BackLog
('PLENITUDE', 'IN ATTESA SWITCH', 'BACKLOG', 'In attesa cambio fornitore'),

-- PLENITUDE - BackLog Partner
('PLENITUDE', 'PRESSO DISTRIBUTORE', 'BACKLOG_PARTNER', 'Presso distributore locale'),

-- WINDTRE - Esiti OK
('WINDTRE', 'ATTIVATA', 'OK', 'SIM/Linea attivata'),
('WINDTRE', 'CONSEGNATA', 'OK', 'SIM consegnata e attiva'),

-- WINDTRE - Esiti KO
('WINDTRE', 'KO', 'KO', 'Pratica KO'),
('WINDTRE', 'RIFIUTATA', 'KO', 'Ordine rifiutato'),

-- WINDTRE - In Attesa
('WINDTRE', 'IN LAVORAZIONE', 'IN_ATTESA', 'Ordine in lavorazione'),

-- WINDTRE - BackLog
('WINDTRE', 'DA SPEDIRE', 'BACKLOG', 'In attesa spedizione SIM'),

-- WINDTRE - BackLog Partner  
('WINDTRE', 'PRESSO CORRIERE', 'BACKLOG_PARTNER', 'Spedizione in corso');

-- Indici per performance
ALTER TABLE `esiti_conversione` ADD FULLTEXT INDEX `ft_esito_originale` (`esito_originale`);

