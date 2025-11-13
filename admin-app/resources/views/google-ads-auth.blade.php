<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticazione Google Ads</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    🔐 Autenticazione Google Ads API
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Autentica i tuoi account Google Ads per accedere alle API
                </p>
            </div>

            <!-- Account Cards -->
            <div class="grid gap-6 md:grid-cols-2">
                
                <!-- Account 1: MeglioQuesto (già autenticato) -->
                <div class="bg-white rounded-lg shadow-md p-6 border-2 border-green-500">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            📧 marketing.digital@meglioquesto.it
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Autenticato
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <p><strong>MCC ID:</strong> 2981176786</p>
                        <p><strong>Account:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>MeglioQuesto Sales (966-937-4086)</li>
                        </ul>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded p-3 text-xs text-green-700">
                        <p>✓ Token salvato e funzionante</p>
                    </div>
                </div>

                <!-- Account 2: NovaHolding (da autenticare) -->
                <div class="bg-white rounded-lg shadow-md p-6 border-2 border-orange-500">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            📧 pasquale.rizzo@novaholding.it
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            ⚠ Da autenticare
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <p><strong>MCC ID:</strong> 2981176786</p>
                        <p><strong>Account:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Novadirect / Risparmiami (761-724-0470)</li>
                            <li>DGT Media (990-158-2709)</li>
                            <li>NovaHolding (539-568-7013) - <em>non pertinente</em></li>
                        </ul>
                    </div>
                    
                    <a href="/oauth/google-ads/2981176786" 
                       class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        🔓 Autentica Account Gruppo Nova
                    </a>
                </div>

                <!-- Account 3: GT Energie (da autenticare) -->
                <div class="bg-white rounded-lg shadow-md p-6 border-2 border-orange-500">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            📧 pasquale.rizzo@novaholding.it
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            ⚠ Da autenticare
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <p><strong>MCC ID:</strong> 3471037697</p>
                        <p><strong>Account:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>GT Energie SRL (858-819-9597)</li>
                        </ul>
                    </div>
                    
                    <a href="/oauth/google-ads/3471037697" 
                       class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        🔓 Autentica Account GT Energie
                    </a>
                </div>

            </div>

            <!-- Istruzioni -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">📝 Istruzioni:</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                    <li><strong>Prima di autenticare:</strong> Verifica di aver effettuato il logout da tutti gli account Google nel browser</li>
                    <li><strong>Clicca su "Autentica Account"</strong> per l'account che vuoi collegare</li>
                    <li><strong>Accedi con l'email corretta</strong>:
                        <ul class="list-disc list-inside ml-6 mt-1">
                            <li>Gruppo Nova + GT Energie → <code class="bg-blue-100 px-1 rounded">pasquale.rizzo@novaholding.it</code></li>
                        </ul>
                    </li>
                    <li><strong>Autorizza l'accesso</strong> quando richiesto da Google</li>
                    <li><strong>Il sistema salverà automaticamente</strong> il token nel database per tutti gli account collegati a quel MCC</li>
                </ol>
            </div>

            <!-- Note Importanti -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-yellow-900 mb-3">⚠️ Note Importanti:</h3>
                <ul class="list-disc list-inside space-y-2 text-sm text-yellow-800">
                    <li>Se ricevi errore "Nessun refresh_token", vai su <a href="https://myaccount.google.com/permissions" target="_blank" class="underline font-medium">myaccount.google.com/permissions</a> e revoca l'accesso all'app, poi riprova</li>
                    <li>Il refresh token viene fornito solo al primo consenso</li>
                    <li>Ogni account manager (MCC) deve essere autenticato separatamente con la sua email</li>
                    <li>Il token è valido per circa 6 mesi, poi andrà rinnovato</li>
                </ul>
            </div>

            <!-- Back Link -->
            <div class="text-center">
                <a href="/admin/dashboard" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Torna alla Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>

