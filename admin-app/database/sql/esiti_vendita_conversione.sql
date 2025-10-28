-- Tabella per la conversione degli esiti vendita
-- Permette di mappare gli esiti specifici delle vendite in esiti standard
-- SENZA UNIQUE CONSTRAINT per gestire tutte le varianti di case (IT scrive in modo inconsistente)
-- La conversione a livello applicativo gestirà il case-insensitive matching

DROP TABLE IF EXISTS `esiti_vendita_conversione`;

CREATE TABLE `esiti_vendita_conversione` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `esito_originale` VARCHAR(255) NOT NULL COMMENT 'Esito specifico dalla vendita (tutte le varianti case-sensitive)',
  `esito_globale` ENUM('OK', 'KO', 'IN_ATTESA', 'ANNULLATI', 'BACKLOG', 'BACKLOG_PARTNER', 'IN_LAVORAZIONE') NOT NULL COMMENT 'Esito standardizzato',
  `note` TEXT NULL COMMENT 'Note aggiuntive sulla conversione',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_esito_originale` (`esito_originale`),
  INDEX `idx_esito_globale` (`esito_globale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversione esiti vendita in esiti globali - supporta multiple varianti case';

-- Popolamento esiti KO
INSERT INTO `esiti_vendita_conversione` (`esito_originale`, `esito_globale`, `note`) VALUES
('KO Definitivo', 'KO', 'Vendita definitivamente fallita'),
('KO DEFINITIVO', 'KO', 'Vendita definitivamente fallita'),
('ko definitivo', 'KO', 'Vendita definitivamente fallita'),
('KO Controllo Dati', 'KO', 'Controllo dati fallito'),
('KO CONTROLLO DATI', 'KO', 'Controllo dati fallito'),
('ko controllo dati', 'KO', 'Controllo dati fallito'),
('KO RECUPERO CONTROLLO DATI', 'KO', 'Recupero controllo dati fallito'),
('ko recupero controllo dati', 'KO', 'Recupero controllo dati fallito'),
('Ko Recall', 'KO', 'Recall fallito'),
('KO RECALL', 'KO', 'Recall fallito'),
('ko recall', 'KO', 'Recall fallito'),
('Ko Vocal', 'KO', 'Vocal fallito'),
('KO VOCAL', 'KO', 'Vocal fallito'),
('ko vocal', 'KO', 'Vocal fallito'),
('KO NON VALIDATO', 'KO', 'Non validato'),
('ko non validato', 'KO', 'Non validato'),
('KO POLIZZA', 'KO', 'Polizza fallita'),
('ko polizza', 'KO', 'Polizza fallita'),

-- Popolamento esiti OK
('OK Definitivo', 'OK', 'Vendita definitivamente OK'),
('OK DEFINITIVO', 'OK', 'Vendita definitivamente OK'),
('ok definitivo', 'OK', 'Vendita definitivamente OK'),
('OK FIRMA', 'OK', 'Firma completata'),
('ok firma', 'OK', 'Firma completata'),
('Ok vocal', 'OK', 'Vocal OK'),
('OK VOCAL', 'OK', 'Vocal OK'),
('ok vocal', 'OK', 'Vocal OK'),
('Ok Recall', 'OK', 'Recall OK'),
('OK RECALL', 'OK', 'Recall OK'),
('ok recall', 'OK', 'Recall OK'),

-- Popolamento esiti IN ATTESA
('In attesa Sblocco', 'IN_ATTESA', 'In attesa di sblocco'),
('IN ATTESA SBLOCCO', 'IN_ATTESA', 'In attesa di sblocco'),
('in attesa sblocco', 'IN_ATTESA', 'In attesa di sblocco'),
('DA FIRMARE', 'IN_ATTESA', 'In attesa di firma'),
('da firmare', 'IN_ATTESA', 'In attesa di firma'),
('In Attesa Iban', 'IN_ATTESA', 'In attesa IBAN'),
('IN ATTESA IBAN', 'IN_ATTESA', 'In attesa IBAN'),
('in attesa iban', 'IN_ATTESA', 'In attesa IBAN'),

-- Popolamento esiti ANNULLATI
('Annullata da Operatore', 'ANNULLATI', 'Annullata da operatore'),
('ANNULLATA DA OPERATORE', 'ANNULLATI', 'Annullata da operatore'),
('annullata da operatore', 'ANNULLATI', 'Annullata da operatore'),
('Annullata da BO o SV', 'ANNULLATI', 'Annullata da back office o supervisore'),
('ANNULLATA DA BO O SV', 'ANNULLATI', 'Annullata da back office o supervisore'),
('annullata da bo o sv', 'ANNULLATI', 'Annullata da back office o supervisore'),
('ANNULLATA', 'ANNULLATI', 'Vendita annullata'),
('annullata', 'ANNULLATI', 'Vendita annullata'),
('Annullata', 'ANNULLATI', 'Vendita annullata'),
('Annullato', 'ANNULLATI', 'Vendita annullata'),
('ANNULLATO', 'ANNULLATI', 'Vendita annullata'),
('annullato', 'ANNULLATI', 'Vendita annullata'),

-- Popolamento esiti BACKLOG
('IRREPERIBILE', 'BACKLOG', 'Cliente irreperibile'),
('irreperibile', 'BACKLOG', 'Cliente irreperibile'),
('Irreperibile', 'BACKLOG', 'Cliente irreperibile'),
('Pratica doppia', 'BACKLOG', 'Pratica duplicata'),
('PRATICA DOPPIA', 'BACKLOG', 'Pratica duplicata'),
('pratica doppia', 'BACKLOG', 'Pratica duplicata'),
('DA RIPROCESSARE', 'BACKLOG', 'Da riprocessare'),
('da riprocessare', 'BACKLOG', 'Da riprocessare'),
('Da Riprocessare', 'BACKLOG', 'Da riprocessare'),
('RIPROPOSTO', 'BACKLOG', 'Riproposto al cliente'),
('riproposto', 'BACKLOG', 'Riproposto al cliente'),
('Riproposto', 'BACKLOG', 'Riproposto al cliente'),
('Acquisito', 'BACKLOG', 'Dati acquisiti'),
('ACQUISITO', 'BACKLOG', 'Dati acquisiti'),
('acquisito', 'BACKLOG', 'Dati acquisiti'),
('Ok inserito', 'BACKLOG', 'OK ma da completare'),
('OK INSERITO', 'BACKLOG', 'OK ma da completare'),
('ok inserito', 'BACKLOG', 'OK ma da completare'),
('DA ASCOLTARE', 'BACKLOG', 'Registrazione da ascoltare'),
('da ascoltare', 'BACKLOG', 'Registrazione da ascoltare'),
('Da Ascoltare', 'BACKLOG', 'Registrazione da ascoltare'),
('PENDING', 'BACKLOG', 'In sospeso'),
('pending', 'BACKLOG', 'In sospeso'),
('Pending', 'BACKLOG', 'In sospeso'),
('In Attesa Firma', 'BACKLOG', 'In attesa firma'),
('IN ATTESA FIRMA', 'BACKLOG', 'In attesa firma'),
('in attesa firma', 'BACKLOG', 'In attesa firma'),
('DA RICARICARE', 'BACKLOG', 'Da ricaricare nel sistema'),
('da ricaricare', 'BACKLOG', 'Da ricaricare nel sistema'),
('Da Ricaricare', 'BACKLOG', 'Da ricaricare nel sistema'),
('in attesa conferma', 'BACKLOG', 'In attesa conferma'),
('IN ATTESA CONFERMA', 'BACKLOG', 'In attesa conferma'),
('In Attesa Conferma', 'BACKLOG', 'In attesa conferma'),
('Ok Controllo dati', 'BACKLOG', 'OK controllo dati - da completare'),
('OK CONTROLLO DATI', 'BACKLOG', 'OK controllo dati - da completare'),
('ok controllo dati', 'BACKLOG', 'OK controllo dati - da completare'),
('Ok Firma Bollettino', 'BACKLOG', 'OK firma bollettino - da completare'),
('OK FIRMA BOLLETTINO', 'BACKLOG', 'OK firma bollettino - da completare'),
('ok firma bollettino', 'BACKLOG', 'OK firma bollettino - da completare'),
('OK FIRMA OTP GAS', 'BACKLOG', 'OK firma OTP gas - da completare'),
('ok firma otp gas', 'BACKLOG', 'OK firma OTP gas - da completare'),
('Ok Firma Otp Gas', 'BACKLOG', 'OK firma OTP gas - da completare'),
('OK FIRMA SWO', 'BACKLOG', 'OK firma SWO - da completare'),
('ok firma swo', 'BACKLOG', 'OK firma SWO - da completare'),
('Ok Firma Swo', 'BACKLOG', 'OK firma SWO - da completare'),

-- Popolamento esiti BACKLOG PARTNER (nota: "In attesa Sblocco" è anche in IN_ATTESA, mantieni coerenza)
-- ('In attesa Sblocco', 'BACKLOG_PARTNER', 'In attesa sblocco partner'),
-- Lascio commentato perché già in IN_ATTESA, da decidere con l'utente

-- Popolamento esiti IN LAVORAZIONE
('BOZZA', 'IN_LAVORAZIONE', 'Vendita in bozza'),
('bozza', 'IN_LAVORAZIONE', 'Vendita in bozza'),
('Bozza', 'IN_LAVORAZIONE', 'Vendita in bozza'),
('IN CORSO', 'IN_LAVORAZIONE', 'Vendita in corso'),
('in corso', 'IN_LAVORAZIONE', 'Vendita in corso'),
('In Corso', 'IN_LAVORAZIONE', 'Vendita in corso');

-- Indici per performance
ALTER TABLE `esiti_vendita_conversione` ADD FULLTEXT INDEX `ft_esito_originale` (`esito_originale`);

