<?php

/**
 * Icon Mapping Configuration
 * 
 * Mappa nomi "semantici" o legacy (FontAwesome) a componenti Heroicons.
 * 
 * Struttura:
 * 'nome-icona' => [
 *     'set' => 'heroicon-o' (outline) | 'heroicon-s' (solid),
 *     'icon' => 'nome-componente-heroicon',
 * ]
 * 
 * Heroicons disponibili: https://heroicons.com/
 * - heroicon-o-* = outline (stroke-based, default)
 * - heroicon-s-* = solid (filled)
 * 
 * Per aggiungere nuove icone:
 * 1. Cerca l'icona su https://heroicons.com/
 * 2. Usa il nome kebab-case (es: arrow-left, user-circle)
 * 3. Aggiungi entry in questo array
 * 
 * @return array<string, array{set: string, icon: string}>
 */
return [
    // === NAVIGAZIONE & AZIONI ===
    'arrow-left' => ['set' => 'heroicon-o', 'icon' => 'arrow-left'],
    'arrow-right' => ['set' => 'heroicon-o', 'icon' => 'arrow-right'],
    'arrow-up' => ['set' => 'heroicon-o', 'icon' => 'arrow-up'],
    'arrow-down' => ['set' => 'heroicon-o', 'icon' => 'arrow-down'],
    'chevron-right' => ['set' => 'heroicon-o', 'icon' => 'chevron-right'],
    'chevron-left' => ['set' => 'heroicon-o', 'icon' => 'chevron-left'],
    'chevron-down' => ['set' => 'heroicon-o', 'icon' => 'chevron-down'],
    'chevron-up' => ['set' => 'heroicon-o', 'icon' => 'chevron-up'],
    
    // === AZIONI COMUNI ===
    'plus' => ['set' => 'heroicon-o', 'icon' => 'plus'],
    'pencil' => ['set' => 'heroicon-o', 'icon' => 'pencil'],
    'edit' => ['set' => 'heroicon-o', 'icon' => 'pencil'],
    'trash' => ['set' => 'heroicon-o', 'icon' => 'trash'],
    'trash-2' => ['set' => 'heroicon-o', 'icon' => 'trash'],
    'download' => ['set' => 'heroicon-o', 'icon' => 'arrow-down-tray'],
    'upload' => ['set' => 'heroicon-o', 'icon' => 'arrow-up-tray'],
    'search' => ['set' => 'heroicon-o', 'icon' => 'magnifying-glass'],
    'filter' => ['set' => 'heroicon-o', 'icon' => 'funnel'],
    'refresh' => ['set' => 'heroicon-o', 'icon' => 'arrow-path'],
    'rotate' => ['set' => 'heroicon-o', 'icon' => 'arrow-path'],
    'save' => ['set' => 'heroicon-o', 'icon' => 'check'],
    'close' => ['set' => 'heroicon-o', 'icon' => 'x-mark'],
    'x' => ['set' => 'heroicon-o', 'icon' => 'x-mark'],
    'times' => ['set' => 'heroicon-o', 'icon' => 'x-mark'],
    
    // === UTENTI & PROFILI ===
    'user' => ['set' => 'heroicon-o', 'icon' => 'user'],
    'users' => ['set' => 'heroicon-o', 'icon' => 'users'],
    'user-plus' => ['set' => 'heroicon-o', 'icon' => 'user-plus'],
    'user-minus' => ['set' => 'heroicon-o', 'icon' => 'user-minus'],
    'user-gear' => ['set' => 'heroicon-o', 'icon' => 'user'],
    'user-circle' => ['set' => 'heroicon-o', 'icon' => 'user-circle'],
    
    // === HOME & NAVIGAZIONE ===
    'home' => ['set' => 'heroicon-o', 'icon' => 'home'],
    'house' => ['set' => 'heroicon-o', 'icon' => 'home'],
    'dashboard' => ['set' => 'heroicon-o', 'icon' => 'squares-2x2'],
    
    // === SETTINGS & SYSTEM ===
    'settings' => ['set' => 'heroicon-o', 'icon' => 'cog-6-tooth'],
    'gear' => ['set' => 'heroicon-o', 'icon' => 'cog-6-tooth'],
    'cog' => ['set' => 'heroicon-o', 'icon' => 'cog-6-tooth'],
    'wrench' => ['set' => 'heroicon-o', 'icon' => 'wrench'],
    
    // === THEME & UI ===
    'sun' => ['set' => 'heroicon-o', 'icon' => 'sun'],
    'moon' => ['set' => 'heroicon-o', 'icon' => 'moon'],
    'bars' => ['set' => 'heroicon-o', 'icon' => 'bars-3'],
    'bars-3' => ['set' => 'heroicon-o', 'icon' => 'bars-3'],
    'computer-desktop' => ['set' => 'heroicon-o', 'icon' => 'computer-desktop'],
    'log-in' => ['set' => 'heroicon-o', 'icon' => 'arrow-right-on-rectangle'],
    'logout' => ['set' => 'heroicon-o', 'icon' => 'arrow-right-on-rectangle'],
    
    // === CALENDARIO & TEMPO ===
    'calendar' => ['set' => 'heroicon-o', 'icon' => 'calendar'],
    'calendar-days' => ['set' => 'heroicon-o', 'icon' => 'calendar-days'],
    'calendar-check' => ['set' => 'heroicon-o', 'icon' => 'calendar'],
    'calendar-xmark' => ['set' => 'heroicon-o', 'icon' => 'calendar'],
    'clock' => ['set' => 'heroicon-o', 'icon' => 'clock'],
    'time' => ['set' => 'heroicon-o', 'icon' => 'clock'],
    
    // === CHARTS & ANALYTICS ===
    'chart-bar' => ['set' => 'heroicon-o', 'icon' => 'chart-bar'],
    'chart-line' => ['set' => 'heroicon-o', 'icon' => 'chart-bar'],
    'chart-pie' => ['set' => 'heroicon-o', 'icon' => 'chart-pie'],
    'chart-area' => ['set' => 'heroicon-o', 'icon' => 'presentation-chart-line'],
    'trending-up' => ['set' => 'heroicon-o', 'icon' => 'arrow-trending-up'],
    'trending-down' => ['set' => 'heroicon-o', 'icon' => 'arrow-trending-down'],
    
    // === DOCUMENTI & FILE ===
    'file' => ['set' => 'heroicon-o', 'icon' => 'document'],
    'file-text' => ['set' => 'heroicon-o', 'icon' => 'document-text'],
    'document' => ['set' => 'heroicon-o', 'icon' => 'document'],
    'folder' => ['set' => 'heroicon-o', 'icon' => 'folder'],
    'clipboard' => ['set' => 'heroicon-o', 'icon' => 'clipboard'],
    'clipboard-list' => ['set' => 'heroicon-o', 'icon' => 'clipboard-document-list'],
    
    // === COMUNICAZIONE ===
    'envelope' => ['set' => 'heroicon-o', 'icon' => 'envelope'],
    'envelope-open-text' => ['set' => 'heroicon-o', 'icon' => 'envelope-open'],
    'mail' => ['set' => 'heroicon-o', 'icon' => 'envelope'],
    'message' => ['set' => 'heroicon-o', 'icon' => 'chat-bubble-left-right'],
    'bell' => ['set' => 'heroicon-o', 'icon' => 'bell'],
    'mobile' => ['set' => 'heroicon-o', 'icon' => 'device-phone-mobile'],
    'phone' => ['set' => 'heroicon-o', 'icon' => 'phone'],
    
    // === BUSINESS & FINANCE ===
    'money-bills' => ['set' => 'heroicon-o', 'icon' => 'banknotes'],
    'banknotes' => ['set' => 'heroicon-o', 'icon' => 'banknotes'],
    'currency-dollar' => ['set' => 'heroicon-o', 'icon' => 'currency-dollar'],
    'wallet' => ['set' => 'heroicon-o', 'icon' => 'wallet'],
    'receipt' => ['set' => 'heroicon-o', 'icon' => 'receipt-percent'],
    'calculator' => ['set' => 'heroicon-o', 'icon' => 'calculator'],
    'file-invoice' => ['set' => 'heroicon-o', 'icon' => 'document-text'],
    'file-invoice-dollar' => ['set' => 'heroicon-o', 'icon' => 'document-currency-dollar'],
    'building-columns' => ['set' => 'heroicon-o', 'icon' => 'building-library'],
    'bank' => ['set' => 'heroicon-o', 'icon' => 'building-library'],
    'briefcase' => ['set' => 'heroicon-o', 'icon' => 'briefcase'],
    'building' => ['set' => 'heroicon-o', 'icon' => 'building-office'],
    
    // === MARKETING ===
    'bullhorn' => ['set' => 'heroicon-o', 'icon' => 'megaphone'],
    'megaphone' => ['set' => 'heroicon-o', 'icon' => 'megaphone'],
    'speakerphone' => ['set' => 'heroicon-o', 'icon' => 'megaphone'],
    
    // === PRODUZIONE & ICT ===
    'industry' => ['set' => 'heroicon-o', 'icon' => 'building-office-2'],
    'desktop' => ['set' => 'heroicon-o', 'icon' => 'computer-desktop'],
    'server' => ['set' => 'heroicon-o', 'icon' => 'server'],
    'code' => ['set' => 'heroicon-o', 'icon' => 'code-bracket'],
    'terminal' => ['set' => 'heroicon-o', 'icon' => 'command-line'],
    'keyboard' => ['set' => 'heroicon-o', 'icon' => 'computer-desktop'],
    
    // === STATUS & FEEDBACK ===
    'check' => ['set' => 'heroicon-o', 'icon' => 'check'],
    'check-circle' => ['set' => 'heroicon-o', 'icon' => 'check-circle'],
    'exclamation' => ['set' => 'heroicon-o', 'icon' => 'exclamation-triangle'],
    'exclamation-circle' => ['set' => 'heroicon-o', 'icon' => 'exclamation-circle'],
    'info' => ['set' => 'heroicon-o', 'icon' => 'information-circle'],
    'question' => ['set' => 'heroicon-o', 'icon' => 'question-mark-circle'],
    'star' => ['set' => 'heroicon-o', 'icon' => 'star'],
    'heart' => ['set' => 'heroicon-o', 'icon' => 'heart'],
    'heartbeat' => ['set' => 'heroicon-o', 'icon' => 'heart'],
    'trophy' => ['set' => 'heroicon-o', 'icon' => 'trophy'],
    
    // === TAGS & LABELS ===
    'tag' => ['set' => 'heroicon-o', 'icon' => 'tag'],
    'tags' => ['set' => 'heroicon-o', 'icon' => 'tag'],
    'ticket' => ['set' => 'heroicon-o', 'icon' => 'ticket'],
    
    // === EDUCATION ===
    'graduation-cap' => ['set' => 'heroicon-o', 'icon' => 'academic-cap'],
    'academic-cap' => ['set' => 'heroicon-o', 'icon' => 'academic-cap'],
    'book' => ['set' => 'heroicon-o', 'icon' => 'book-open'],
    
    // === VARIE ===
    'target' => ['set' => 'heroicon-o', 'icon' => 'arrow-trending-up'],
    'bullseye' => ['set' => 'heroicon-s', 'icon' => 'arrow-trending-up'],
    'eye' => ['set' => 'heroicon-o', 'icon' => 'eye'],
    'eye-slash' => ['set' => 'heroicon-o', 'icon' => 'eye-slash'],
    'lock' => ['set' => 'heroicon-o', 'icon' => 'lock-closed'],
    'unlock' => ['set' => 'heroicon-o', 'icon' => 'lock-open'],
    'shield' => ['set' => 'heroicon-o', 'icon' => 'shield-check'],
    'key' => ['set' => 'heroicon-o', 'icon' => 'key'],
    'link' => ['set' => 'heroicon-o', 'icon' => 'link'],
    'share' => ['set' => 'heroicon-o', 'icon' => 'share'],
    'print' => ['set' => 'heroicon-o', 'icon' => 'printer'],
    'list-check' => ['set' => 'heroicon-o', 'icon' => 'clipboard-document-check'],
    'arrow-right-arrow-left' => ['set' => 'heroicon-o', 'icon' => 'arrow-path-rounded-square'],
    'info-circle' => ['set' => 'heroicon-o', 'icon' => 'information-circle'],
    'exclamation-triangle' => ['set' => 'heroicon-o', 'icon' => 'exclamation-triangle'],
    
    // === FALLBACK ===
    'default' => ['set' => 'heroicon-o', 'icon' => 'question-mark-circle'],
];

