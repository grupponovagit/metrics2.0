#!/bin/bash

# ============================================
# RESET COMPLETO PROGETTO METRICS 2.0
# ============================================
# Questo script esegue un reset completo del progetto
# ⚠️  ATTENZIONE: Cancella TUTTI i dati del database!
# ============================================

set -e  # Exit on error

echo "🚀 RESET PROGETTO METRICS 2.0"
echo "================================"
echo ""
echo "⚠️  ATTENZIONE: Questo script:"
echo "   - Cancellerà TUTTI i dati del database"
echo "   - Pulirà tutte le cache"
echo "   - Ricostruirà il database da zero"
echo ""
read -p "Sei sicuro di voler continuare? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "❌ Operazione annullata"
    exit 1
fi

echo ""
echo "✨ Inizio reset..."
echo ""

# 1. Pulizia cache
echo "🧹 Step 1/5: Pulizia cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan permission:cache-reset
echo "✅ Cache pulita"
echo ""

# 2. Reset database
echo "🗄️  Step 2/5: Reset database..."
php artisan migrate:fresh --seed --seeder=CompleteSystemSeeder
echo "✅ Database resettato e popolato"
echo ""

# 3. Ricrea cache ottimizzata
echo "⚡ Step 3/5: Ricreazione cache ottimizzata..."
php artisan config:cache
php artisan route:cache
echo "✅ Cache ottimizzata"
echo ""

# 4. Rebuild assets (opzionale)
echo "🎨 Step 4/5: Rebuild assets frontend..."
if command -v npm &> /dev/null
then
    npm run build
    echo "✅ Asset compilati"
else
    echo "⚠️  NPM non trovato, skip build assets"
fi
echo ""

# 5. Verifica finale
echo "🔍 Step 5/5: Verifica finale..."
php artisan about
echo ""

# Riepilogo
echo ""
echo "================================"
echo "✅ RESET COMPLETATO CON SUCCESSO!"
echo "================================"
echo ""
echo "📋 Operazioni eseguite:"
echo "   ✅ Cache pulita"
echo "   ✅ Database resettato"
echo "   ✅ Ruoli e permessi creati"
echo "   ✅ Utenti di test caricati (se presente JSON)"
echo "   ✅ Asset frontend compilati"
echo ""
echo "🌐 Puoi ora accedere al sistema:"
echo "   URL: http://localhost:8000"
echo "   Oppure: php artisan serve"
echo ""
echo "👤 Credenziali di accesso (se TestUsersSeeder attivo):"
echo "   Controlla: storage/app/seed_real_users.json"
echo ""
echo "📚 Per maggiori info: SETUP.md"
echo ""

