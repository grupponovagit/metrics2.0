#!/bin/bash

echo "🧹 Pulizia cache Laravel..."
php artisan optimize:clear

echo ""
echo "📦 Cache configurazione..."
php artisan config:cache

echo ""
echo "🛣️  Cache routes..."
php artisan route:cache

echo ""
echo "👁️  Cache views..."
php artisan view:cache

echo ""
echo "🎨 Cache blade icons..."
php artisan blade-icons:cache

echo ""
echo "📚 Ottimizzazione autoload Composer..."
composer dump-autoload -o

echo ""
echo "✅ Progetto pulito e ottimizzato con successo!"
echo ""

