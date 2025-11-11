# 📊 Cruscotto Lead Marketing - Documentazione

## ✅ Completato

### 1. **Controller** (`MarketingController.php`)
- ✅ Metodo `cruscottoLead()` con logica di filtro dinamico
- ✅ Calcolo KPI (CPL, CPA, CPC, ROAS, ROI)
- ✅ Filtri concatenati: Ragione Sociale → Provenienza → Campagne
- ✅ Query su tabella `report_digital_leads`

### 2. **Vista Principale** (`index.blade.php`)
- ✅ Filtri con **checkbox** invece di select multiple
- ✅ Pulsanti "Tutte/Nessuna" per ogni filtro
- ✅ Aggiornamento dinamico campagne via AJAX
- ✅ Switch tra viste: Sintetico / Dettagliato / Giornaliero
- ✅ **Dropdown Colonne con z-index alto** (z-50 e z-100)

### 3. **Tabelle**
- ✅ **Dettagliato**: Ragione Sociale → Provenienza → Campagna (con rowspan)
- ✅ **Sintetico**: Ragione Sociale → Provenienza (aggregato)
- ✅ **Giornaliero**: Data (aggregato per giorno)

### 4. **Colori Colonne** (Diversi per distinguere)
- 🔵 **Blu** (`bg-blue-100`): Costo
- 🟢 **Verde** (`bg-green-100`): Lead, Ricavi
- 🐚 **Teal** (`bg-teal-100`): Conversioni (Conv, OK Lead, KO Lead)
- 🟡 **Amber** (`bg-amber-100`): Economics (CPL, CPA, CPC)
- 🟣 **Viola** (`bg-purple-100`): Performance (ROAS, ROI)
- 🔷 **Cyan** (`bg-cyan-100`): Click
- 🟦 **Indigo** (`bg-indigo-100`): Ore

### 5. **Script** (`_scripts.blade.php`)
- ✅ Switch tra viste (sintetico/dettagliato/giornaliero)
- ✅ Toggle colonne (mostra/nascondi)
- ✅ Label dinamico "Colonne (X/Y)"

### 6. **Stili** (`_styles.blade.php`)
- ✅ Copiato da produzione
- ✅ Sticky header e totale
- ✅ Responsive e scroll

---

## 📊 Struttura Dati

### Tabella DB: `report_digital_leads`
```
- data (date)
- ragione_sociale (varchar 50)
- provenienza (varchar 100)
- utm_campaign (varchar 255)
- account_id (varchar 50)
- tipo_lavorazione (varchar 100)
- costo (decimal 12,2)
- leads (int)
- conv (int)
- ok_lead (int)
- ko_lead (int)
- click (int)
- ore (decimal 10,2)
- ricavi (decimal 12,2)
- cpl (decimal 10,2)  → Costo Per Lead
- cpa (decimal 10,2)  → Costo Per Acquisizione
- cpc (decimal 10,2)  → Costo Per Click
- roas (decimal 10,2) → Return On Ad Spend (%)
- roi (decimal 10,2)  → Return On Investment (%)
```

---

## 🎨 Colori per Macro-Colonne

| Macro Colonna | Colore | Sottocolonne |
|---------------|--------|--------------|
| **Economics** | Amber (🟡) | CPL, CPA, CPC |
| **Performance** | Viola (🟣) | ROAS %, ROI % |
| **Conversioni** | Teal (🐚) | Conv., OK Lead, KO Lead |
| **Costo** | Blu (🔵) | - |
| **Lead** | Verde (🟢) | - |
| **Click** | Cyan (🔷) | - |
| **Ore** | Indigo (🟦) | - |
| **Ricavi** | Verde (🟢) | - |

---

## 🔧 Funzionalità JavaScript

### Toggle Filtri
```javascript
toggleAllRagioneSociale(true/false)  // Seleziona/Deseleziona tutte
toggleAllProvenienza(true/false)
toggleAllCampagne(true/false)
```

### Update Dinamico
```javascript
updateCampagneFilter()  // Aggiorna campagne in base a filtri attivi
```

### Switch Viste
```javascript
switchView('sintetico')
switchView('dettagliato')
switchView('giornaliero')
```

### Toggle Colonne
```javascript
toggleColumn('leads')
toggleColumn('conversioni')
toggleColumn('economics')
toggleColumn('performance')
toggleColumn('click')
toggleColumn('ore')
toggleColumn('tutte')     // Mostra tutte
toggleColumn('nessuna')   // Nascondi tutte
```

---

## 📍 Rotta

```
GET /admin/marketing/cruscotto-lead
Nome: admin.marketing.cruscotto_lead
Controller: MarketingController@cruscottoLead
```

---

## 🧪 Come Testare

1. **Accedi alla dashboard**: `http://127.0.0.1:8000/admin/marketing/cruscotto-lead`
2. **Seleziona periodo**: Data Inizio e Data Fine
3. **Filtra per Ragione Sociale**: Checkbox multiple
4. **Filtra per Provenienza**: Checkbox multiple
5. **Filtra per Campagne**: Checkbox multiple (dinamiche)
6. **Clicca "Applica Filtri"**
7. **Switch tra viste**: Sintetico / Dettagliato / Giornaliero
8. **Gestisci colonne**: Dropdown "Colonne" in alto a destra

---

## 🎯 Note Importanti

- **Z-index Dropdown**: Impostato a `z-50` (wrapper) e `z-[100]` (menu) per evitare sovrapposizioni
- **Checkbox invece di Select**: Migliore UX per selezione multipla
- **Filtri Concatenati**: Campagne si aggiornano dinamicamente in base a Ragione Sociale e Provenienza
- **KPI Precalcolati**: CPL, CPA, CPC, ROAS, ROI sono già nella tabella DB
- **Rowspan**: Ragione Sociale e Provenienza usano rowspan nella vista dettagliata per raggruppare

---

## 🚀 Prossimi Passi

1. ✅ Popolare la tabella `report_digital_leads` con dati reali
2. ✅ Testare i filtri e le viste
3. ✅ Verificare i calcoli KPI
4. ⏳ Eventuale aggiunta di grafico (già presente `_table-grafico.blade.php`)
