<x-admin.wrapper>
    <x-slot name="title">Google Ads API - Autenticazioni</x-slot>

    <x-admin.page-header 
        title="Google Ads API" 
        subtitle="Gestione autenticazioni OAuth per accedere alle Google Ads API"
        icon="google"
        iconColor="primary"
    />

    {{-- Account Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($mccGroups as $group)
        <x-admin.card tone="light" shadow="lg" class="group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 -mr-16 -mt-16 rounded-full opacity-10 {{ $group['token_valid'] ? 'bg-success' : ($group['token_status'] === 'invalid' ? 'bg-error' : 'bg-warning') }}"></div>
            
            <div class="relative">
                {{-- Header con Badge --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-12 h-12 {{ $group['token_valid'] ? 'bg-success/10' : ($group['token_status'] === 'invalid' ? 'bg-error/10' : 'bg-warning/10') }} rounded-xl flex items-center justify-center">
                            <i class="fab fa-google text-2xl {{ $group['token_valid'] ? 'text-success' : ($group['token_status'] === 'invalid' ? 'text-error' : 'text-warning') }}"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-base-content">
                                MCC {{ $group['mcc_id'] }}
                            </h3>
                            <p class="text-xs text-base-content/60">
                                {{ $group['email'] }}
                            </p>
                        </div>
                    </div>
                    @if($group['token_status'] === 'valid')
                        <span class="badge badge-success badge-sm gap-1">
                            <i class="fas fa-check text-xs"></i>
                            Attivo
                        </span>
                    @elseif($group['token_status'] === 'invalid')
                        <span class="badge badge-error badge-sm gap-1">
                            <i class="fas fa-times-circle text-xs"></i>
                            Scaduto
                        </span>
                    @else
                        <span class="badge badge-warning badge-sm gap-1">
                            <i class="fas fa-exclamation-triangle text-xs"></i>
                            Mancante
                        </span>
                    @endif
                </div>

                {{-- Info Developer Token --}}
                @if($group['developer_token'])
                <div class="mb-4 p-3 bg-base-200/50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-key text-xs text-base-content/60"></i>
                        <span class="text-xs font-semibold text-base-content/80">Developer Token</span>
                    </div>
                    <code class="text-xs text-base-content/60 font-mono">{{ substr($group['developer_token'], 0, 20) }}...</code>
                </div>
                @endif

                {{-- Scadenza Token o Alert Scaduto --}}
                @if($group['token_status'] === 'valid' && $group['token_expires'])
                <div class="mb-4 p-3 bg-success/5 border border-success/20 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-success"></i>
                        <div>
                            <p class="text-xs font-semibold text-success">Token attivo e valido</p>
                            <p class="text-xs text-base-content/60">
                                Scade il {{ \Carbon\Carbon::parse($group['token_expires'])->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($group['token_status'] === 'invalid')
                <div class="mb-4 p-3 bg-error/5 border border-error/20 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-error"></i>
                        <div>
                            <p class="text-xs font-semibold text-error">Token scaduto o revocato</p>
                            <p class="text-xs text-base-content/60">
                                È necessaria una nuova autenticazione
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Account Collegati --}}
                <div class="mb-4">
                    <p class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
                        <i class="fas fa-link text-xs"></i>
                        Account collegati ({{ count($group['accounts']) }})
                    </p>
                    <div class="space-y-1">
                        @foreach($group['accounts'] as $account)
                        <div class="flex items-center gap-2 text-xs">
                            <div class="w-1.5 h-1.5 rounded-full {{ $group['token_valid'] ? 'bg-success' : 'bg-base-content/20' }}"></div>
                            <span class="text-base-content/70">{{ $account->ragione_sociale }}</span>
                            <span class="text-base-content/40">({{ $account->account_id }})</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Button --}}
                <a href="/oauth/google-ads/{{ $group['mcc_id'] }}" 
                   class="btn btn-block {{ $group['token_valid'] ? 'btn-primary' : 'btn-error' }} btn-sm gap-2 group-hover:scale-[1.02] transition-transform">
                    <i class="fas {{ $group['token_valid'] ? 'fa-sync-alt' : 'fa-unlock' }}"></i>
                    {{ $group['token_valid'] ? 'Rigenera Token' : ($group['token_status'] === 'invalid' ? 'Token Scaduto - Rigenera' : 'Autentica Ora') }}
                </a>
            </div>
        </x-admin.card>
        @endforeach
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Istruzioni --}}
        <x-admin.card tone="info" shadow="md">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-info rounded-xl flex items-center justify-center">
                    <i class="fas fa-info-circle text-info-content"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-3 text-base-content">Come autenticare</h3>
                    <ol class="space-y-2 text-sm text-base-content/80">
                        <li class="flex gap-2">
                            <span class="badge badge-info badge-sm flex-shrink-0 mt-0.5">1</span>
                            <span>Assicurati di aver fatto logout da tutti gli account Google (o usa incognito)</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="badge badge-info badge-sm flex-shrink-0 mt-0.5">2</span>
                            <span>Clicca su "Autentica Ora" per l'account desiderato</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="badge badge-info badge-sm flex-shrink-0 mt-0.5">3</span>
                            <span>Accedi con l'email Google corretta mostrata nella card</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="badge badge-info badge-sm flex-shrink-0 mt-0.5">4</span>
                            <span>Autorizza l'accesso quando richiesto</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="badge badge-info badge-sm flex-shrink-0 mt-0.5">5</span>
                            <span>Il token verrà salvato automaticamente per tutti gli account del MCC</span>
                        </li>
                    </ol>
                </div>
            </div>
        </x-admin.card>

        {{-- Note Importanti --}}
        <x-admin.card tone="warning" shadow="md">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-warning rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-warning-content"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-3 text-base-content">Note importanti</h3>
                    <ul class="space-y-2 text-sm text-base-content/80">
                        <li class="flex gap-2">
                            <i class="fas fa-circle text-[6px] text-warning mt-1.5 flex-shrink-0"></i>
                            <span>I token sono validi per circa <strong>6 mesi</strong></span>
                        </li>
                        <li class="flex gap-2">
                            <i class="fas fa-circle text-[6px] text-warning mt-1.5 flex-shrink-0"></i>
                            <span>Ogni MCC richiede autenticazione separata</span>
                        </li>
                        <li class="flex gap-2">
                            <i class="fas fa-circle text-[6px] text-warning mt-1.5 flex-shrink-0"></i>
                            <span>Se vedi "Nessun refresh_token": 
                                <a href="https://myaccount.google.com/permissions" target="_blank" class="link link-warning font-semibold">
                                    revoca l'accesso
                                </a> e riprova
                            </span>
                        </li>
                        <li class="flex gap-2">
                            <i class="fas fa-circle text-[6px] text-warning mt-1.5 flex-shrink-0"></i>
                            <span>Usa "Rigenera Token" per rinnovare prima della scadenza</span>
                        </li>
                    </ul>
                </div>
            </div>
        </x-admin.card>
    </div>

</x-admin.wrapper>


