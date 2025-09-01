# 🎨 FontAwesome Icons - Guida all'Uso

FontAwesome è ora disponibile in tutta l'applicazione tramite CDN. Puoi utilizzare le icone in due modi:

## 📋 Componenti Disponibili

### 1. Componente Base (`<x-fa-icon>`)
Per uso generale nell'applicazione:

```blade
<x-fa-icon name="home" />
<x-fa-icon name="user" size="lg" />
<x-fa-icon name="heart" color="red-500" />
<x-fa-icon name="star" style="regular" />
```

### 2. Componente Admin (`<x-admin.fa-icon>`)
Per l'area admin con funzionalità aggiuntive:

```blade
<x-admin.fa-icon name="dashboard" />
<x-admin.fa-icon name="spinner" spin="true" />
<x-admin.fa-icon name="heart" pulse="true" />
<x-admin.fa-icon name="check" fixed="true" />
```

## 🔧 Parametri Disponibili

| Parametro | Descrizione | Esempio |
|-----------|-------------|---------|
| `name` | Nome dell'icona (senza fa-) | `"home"`, `"user"`, `"settings"` |
| `style` | Stile dell'icona | `"solid"`, `"regular"`, `"light"`, `"brands"` |
| `size` | Dimensione | `"xs"`, `"sm"`, `"lg"`, `"xl"`, `"2x"`, `"3x"` |
| `color` | Colore Tailwind | `"red-500"`, `"blue-600"`, `"green-400"` |
| `class` | Classi CSS aggiuntive | `"mr-2"`, `"hover:text-blue-500"` |
| `spin` | Rotazione continua (solo admin) | `true`/`false` |
| `pulse` | Pulsazione (solo admin) | `true`/`false` |
| `fixed` | Larghezza fissa (solo admin) | `true`/`false` |

## 🎯 Esempi Pratici

### Icone del Menu
```blade
<x-admin.fa-icon name="tachometer-alt" class="mr-3" /> Dashboard
<x-admin.fa-icon name="users" class="mr-3" /> Utenti
<x-admin.fa-icon name="cog" class="mr-3" /> Impostazioni
```

### Pulsanti con Icone
```blade
<button class="btn btn-primary">
    <x-admin.fa-icon name="plus" class="mr-2" />
    Aggiungi Nuovo
</button>

<button class="btn btn-danger">
    <x-admin.fa-icon name="trash" class="mr-2" />
    Elimina
</button>
```

### Icone di Stato
```blade
<!-- Loading -->
<x-admin.fa-icon name="spinner" spin="true" class="text-blue-500" />

<!-- Successo -->
<x-admin.fa-icon name="check-circle" class="text-green-500" />

<!-- Errore -->
<x-admin.fa-icon name="exclamation-triangle" class="text-red-500" />
```

### Icone Social
```blade
<x-fa-icon name="facebook" style="brands" class="text-blue-600" />
<x-fa-icon name="twitter" style="brands" class="text-blue-400" />
<x-fa-icon name="instagram" style="brands" class="text-pink-500" />
```

## 🔍 Icone Consigliate per Sezioni Comuni

### Dashboard & Navigation
- `tachometer-alt` - Dashboard
- `home` - Home
- `bars` - Menu hamburger
- `xmark` - Chiudi
- `chevron-down` - Dropdown

### Utenti & Profili
- `user` - Utente singolo
- `users` - Utenti multipli
- `user-circle` - Profilo utente
- `id-card` - ID/Badge

### Contenuti
- `file-alt` - Documenti
- `image` - Immagini
- `video` - Video
- `folder` - Cartelle

### Azioni
- `plus` - Aggiungi
- `edit` - Modifica
- `trash` - Elimina
- `save` - Salva
- `search` - Cerca

### Stato & Feedback
- `check` - Successo
- `times` - Errore
- `exclamation-triangle` - Attenzione
- `info-circle` - Informazione

## 🌐 Risorse

- [FontAwesome Gallery](https://fontawesome.com/icons)
- [FontAwesome Cheatsheet](https://fontawesome.com/cheatsheet)
- Versione utilizzata: **6.5.1**

## 💡 Note

1. Le icone sono caricate tramite CDN per prestazioni ottimali
2. Tutti i layout principali includono FontAwesome
3. I componenti sono ottimizzati per Tailwind CSS e DaisyUI
4. Preferisci sempre i componenti alle classi dirette per consistenza
