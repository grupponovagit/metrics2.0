# 🔧 GUIDA RISOLUZIONE TOKEN GOOGLE ADS SCADUTI

## 📊 Problema Identificato

Tutti i token OAuth2 di Google Ads sono stati **revocati o scaduti**. Questo impedisce l'importazione automatica delle metriche delle campagne Google Ads.

### Account Coinvolti:
- **MCC 2981176786**: MeglioQuesto Sales (966-937-4086)
- **MCC 8089133519**: Novadirect, DGT Media, Novaholding (3 account)
- **MCC 3471037697**: GT Energie (858-819-9597)

**Totale: 5 account Google Ads non funzionanti**

---

## 🚀 Soluzioni Disponibili

### ✅ **SOLUZIONE 1: Via Interfaccia Web (RACCOMANDATA)**

1. **Accedi alla pagina di gestione OAuth:**
   ```
   http://127.0.0.1:8000/admin/ict/google-ads-api
   ```
   (Oppure vai su **Admin → ICT → Google Ads API**)

2. **Visualizza lo stato dei token:**
   - I token scaduti saranno contrassegnati in **ROSSO** con badge "Scaduto"
   - I token validi saranno in **VERDE** con badge "Attivo"

3. **Per ogni MCC con token scaduto:**
   - Clicca su **"Token Scaduto - Rigenera"**
   - Verrai reindirizzato a Google OAuth
   - **IMPORTANTE**: Accedi con l'account Google corretto:
     - MCC 2981176786 → `marketing.digital@meglioquesto.it`
     - MCC 8089133519 → `pasquale.rizzo@novaholding.it`
     - MCC 3471037697 → `pasquale.rizzo@novaholding.it`
   - Autorizza l'accesso
   - Verrai reindirizzato al callback e il token verrà salvato

4. **Verifica che il badge sia diventato VERDE**

---

### 🖥️ **SOLUZIONE 2: Via Terminale**

#### Passo 1: Diagnosi Completa
```bash
cd /Applications/MAMP/htdocs/metrics2.0/admin-app
php artisan googleads:fix-tokens
```

Questo comando:
- Verifica quali MCC hanno token invalidi
- Mostra i link diretti per ri-autenticare
- Fornisce istruzioni passo-passo

#### Passo 2: Ri-autenticazione
Il comando ti mostrerà URL come:
```
http://localhost/oauth/google-ads/2981176786
```

Puoi aprirli direttamente con:
```bash
open "http://localhost/oauth/google-ads/2981176786"
```

Oppure copia-incolla nel browser.

---

## ⚠️ IMPORTANTE: Se ottieni "Nessun refresh_token ricevuto"

Questo succede se Google ha già un token precedente salvato. **Devi prima revocare l'accesso:**

1. **Vai a:** https://myaccount.google.com/permissions
2. **Trova l'applicazione** relativa a Google Ads (il nome corrisponde al tuo progetto OAuth)
3. **Clicca su "Rimuovi accesso"**
4. **Riprova l'autenticazione** (ora Google ti darà un nuovo refresh token)

---

## 🔍 Verifica Post-Fix

Dopo aver ri-autenticato tutti gli MCC, verifica che tutto funzioni:

### Test Manuale Import:
```bash
php artisan googleads:import-date 2025-11-20 --account=966-937-4086
```

Se vedi:
```
✅ Importate X campagne
```
Significa che il token funziona!

### Test Diagnostico:
```bash
php artisan googleads:diagnostic
```

Dovrebbe mostrare tutti gli account con:
```
Status: PRONTO
✅ Refresh Token: Presente
```

---

## 🤖 Import Automatico

Una volta risolti i token, gli import automatici riprenderanno a funzionare. Assicurati che il cron sia attivo:

### Verifica Cron:
```bash
crontab -l | grep googleads
```

Dovrebbe esserci qualcosa come:
```
0 * * * * php /path/to/artisan googleads:update-today
0 6 * * * php /path/to/artisan googleads:import-yesterday
```

---

## 📋 Checklist Completa

- [ ] Apri la pagina web OAuth: `/admin/ict/google-ads-api`
- [ ] Identifica i token con badge ROSSO "Scaduto"
- [ ] Per ogni MCC:
  - [ ] Clicca "Token Scaduto - Rigenera"
  - [ ] Accedi con l'account Google corretto
  - [ ] Autorizza l'accesso
  - [ ] Verifica badge VERDE "Attivo"
- [ ] Testa l'import manuale con `php artisan googleads:import-date`
- [ ] Verifica che i log non mostrino più errori `invalid_grant`

---

## 🆘 Troubleshooting

### Problema: "invalid_client" durante OAuth
**Soluzione**: Verifica che le credenziali OAuth nel `.env` siano corrette:
```env
GOOGLE_ADS_CLIENT_ID=...
GOOGLE_ADS_CLIENT_SECRET=...
GOOGLE_ADS_REDIRECT_URI=http://localhost/oauth/google-ads/callback
```

### Problema: "redirect_uri_mismatch"
**Soluzione**: Aggiungi l'URI di callback nella Google Cloud Console:
- Vai su: https://console.cloud.google.com/apis/credentials
- Modifica le credenziali OAuth 2.0
- Aggiungi `http://localhost/oauth/google-ads/callback`

### Problema: Token salvato ma ancora errori
**Soluzione**: Pulisci la cache:
```bash
php artisan cache:clear
php artisan config:clear
```

---

## 📞 Comandi Utili di Riferimento

| Comando | Descrizione |
|---------|-------------|
| `php artisan googleads:fix-tokens` | Diagnostica e fornisce link per ri-autenticare |
| `php artisan googleads:diagnostic` | Mostra stato di tutti gli account |
| `php artisan googleads:import-date YYYY-MM-DD` | Import manuale per una data |
| `php artisan googleads:import-yesterday` | Import di ieri |
| `php artisan googleads:update-today` | Aggiorna dati di oggi |

---

## ✅ Fine

Una volta completati tutti i passaggi, gli import automatici delle campagne Google Ads riprenderanno a funzionare correttamente!

