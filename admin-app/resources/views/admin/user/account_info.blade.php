<x-admin.wrapper title="Il Mio Account" :containerless="true">
    
    <div class="px-4 sm:px-6 lg:px-10 py-6 space-y-8">
        
        {{-- Page Header --}}
        <x-admin.page-header 
            title="Profilo Utente" 
            subtitle="Gestisci le tue informazioni personali e la sicurezza del tuo account"
            icon="user"
            icon-color="primary"
        />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Card: Info Account --}}
            <x-admin.card tone="light" shadow="lg">
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-base-300">
                        <div class="p-3 bg-primary/10 rounded-xl">
                            <x-ui.icon name="user" size="lg" class="text-primary" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-base-content">Info Account</h2>
                            <p class="text-sm text-base-content/60">Aggiorna nome ed email</p>
                        </div>
                    </div>

                    {{-- Messages Account --}}
                    @if ($errors->account->any())
                        <div class="alert alert-error shadow-lg">
                            <x-ui.icon name="exclamation" size="md" />
                            <div>
                                <h3 class="font-bold">Errori di validazione</h3>
                                <ul class="text-sm mt-1 list-disc list-inside">
                                    @foreach ($errors->account->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    @if (session()->has('account_message'))
                        <div class="alert alert-success shadow-lg">
                            <x-ui.icon name="check-circle" size="md" />
                            <span>{{ session()->get('account_message') }}</span>
                        </div>
                    @endif

                    {{-- Form Account Info --}}
                    <form method="POST" action="{{ route('admin.account.info.store') }}" class="space-y-4">
                        @csrf

                        {{-- Nome --}}
                        <div>
                            <x-input-label for="name" value="Nome" class="mb-2 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                                    <x-ui.icon name="user" size="md" />
                                </div>
                                <x-text-input 
                                    id="name" 
                                    name="name" 
                                    type="text"
                                    :value="old('name', $user->name)"
                                    required
                                    class="w-full pl-12 h-12 {{ $errors->account->has('name') ? 'border-error' : '' }}"
                                    placeholder="Il tuo nome completo"
                                />
                            </div>
                            @if ($errors->account->has('name'))
                                <p class="mt-1 text-sm text-error">{{ $errors->account->first('name') }}</p>
                            @endif
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" value="Email" class="mb-2 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                                    <x-ui.icon name="envelope" size="md" />
                                </div>
                                <x-text-input 
                                    id="email" 
                                    name="email" 
                                    type="email"
                                    :value="old('email', $user->email)"
                                    required
                                    class="w-full pl-12 h-12 {{ $errors->account->has('email') ? 'border-error' : '' }}"
                                    placeholder="tu@esempio.com"
                                />
                            </div>
                            @if ($errors->account->has('email'))
                                <p class="mt-1 text-sm text-error">{{ $errors->account->first('email') }}</p>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end pt-4">
                            <button 
                                type="submit" 
                                class="btn btn-primary gap-2 shadow-lg hover:shadow-xl"
                            >
                                <x-ui.icon name="save" size="md" />
                                Aggiorna Info
                            </button>
                        </div>
                    </form>
                </div>
            </x-admin.card>

            {{-- Card: Cambia Password --}}
            <x-admin.card tone="light" shadow="lg">
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-base-300">
                        <div class="p-3 bg-warning/10 rounded-xl">
                            <x-ui.icon name="lock" size="lg" class="text-warning" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-base-content">Sicurezza</h2>
                            <p class="text-sm text-base-content/60">Cambia la tua password</p>
                        </div>
                    </div>

                    {{-- Messages Password --}}
                    @if ($errors->password->any())
                        <div class="alert alert-error shadow-lg">
                            <x-ui.icon name="exclamation" size="md" />
                            <div>
                                <h3 class="font-bold">Errori di validazione</h3>
                                <ul class="text-sm mt-1 list-disc list-inside">
                                    @foreach ($errors->password->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    @if (session()->has('password_message'))
                        <div class="alert alert-success shadow-lg">
                            <x-ui.icon name="check-circle" size="md" />
                            <span>{{ session()->get('password_message') }}</span>
                        </div>
                    @endif

                    {{-- Form Change Password --}}
                    <form method="POST" action="{{ route('admin.account.password.store') }}" class="space-y-4">
                        @csrf

                        {{-- Vecchia Password --}}
                        <div>
                            <x-input-label for="old_password" value="Vecchia Password" class="mb-2 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                                    <x-ui.icon name="lock" size="md" />
                                </div>
                                <x-text-input 
                                    id="old_password" 
                                    name="old_password" 
                                    type="password"
                                    required
                                    class="w-full pl-12 h-12 {{ $errors->password->has('old_password') ? 'border-error' : '' }}"
                                    placeholder="••••••••"
                                />
                            </div>
                            @if ($errors->password->has('old_password'))
                                <p class="mt-1 text-sm text-error">{{ $errors->password->first('old_password') }}</p>
                            @endif
                        </div>

                        {{-- Nuova Password --}}
                        <div>
                            <x-input-label for="new_password" value="Nuova Password" class="mb-2 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                                    <x-ui.icon name="lock" size="md" />
                                </div>
                                <x-text-input 
                                    id="new_password" 
                                    name="new_password" 
                                    type="password"
                                    required
                                    class="w-full pl-12 h-12 {{ $errors->password->has('new_password') ? 'border-error' : '' }}"
                                    placeholder="Minimo 8 caratteri"
                                />
                            </div>
                            @if ($errors->password->has('new_password'))
                                <p class="mt-1 text-sm text-error">{{ $errors->password->first('new_password') }}</p>
                            @endif
                        </div>

                        {{-- Conferma Password --}}
                        <div>
                            <x-input-label for="confirm_password" value="Conferma Password" class="mb-2 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                                    <x-ui.icon name="lock" size="md" />
                                </div>
                                <x-text-input 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    type="password"
                                    required
                                    class="w-full pl-12 h-12 {{ $errors->password->has('confirm_password') ? 'border-error' : '' }}"
                                    placeholder="Ripeti la nuova password"
                                />
                            </div>
                            @if ($errors->password->has('confirm_password'))
                                <p class="mt-1 text-sm text-error">{{ $errors->password->first('confirm_password') }}</p>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end pt-4">
                            <button 
                                type="submit" 
                                class="btn btn-warning gap-2 shadow-lg hover:shadow-xl"
                            >
                                <x-ui.icon name="shield" size="md" />
                                Cambia Password
                            </button>
                        </div>
                    </form>
                </div>
            </x-admin.card>

        </div>

        {{-- Info Card Sicurezza --}}
        <x-admin.card tone="light" shadow="md">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-info/10 rounded-xl">
                    <x-ui.icon name="info" size="lg" class="text-info" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-base-content mb-2">Consigli per la sicurezza</h3>
                    <ul class="space-y-1 text-sm text-base-content/70">
                        <li class="flex items-start gap-2">
                            <x-ui.icon name="check" size="sm" class="text-success mt-0.5" />
                            <span>Usa una password di almeno 8 caratteri con lettere maiuscole, minuscole, numeri e simboli</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-ui.icon name="check" size="sm" class="text-success mt-0.5" />
                            <span>Non riutilizzare password già usate su altri siti</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-ui.icon name="check" size="sm" class="text-success mt-0.5" />
                            <span>Cambia la password regolarmente (ogni 3-6 mesi)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </x-admin.card>

    </div>

</x-admin.wrapper>
