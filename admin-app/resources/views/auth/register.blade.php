<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-primary to-secondary rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl">
            <x-ui.icon name="user-plus" size="xl" class="text-white" />
        </div>
        <h2 class="text-3xl font-bold text-base-content mb-2">Crea il tuo Account</h2>
        <p class="text-base-content/60">Registrati per iniziare a usare Metrics 2.0</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome Completo')" class="text-base-content font-semibold mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                    <x-ui.icon name="user" size="md" />
                </div>
                <x-text-input 
                    id="name" 
                    class="input input-bordered w-full pl-12 h-12 bg-base-100 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    placeholder="Mario Rossi"
                    required 
                    autofocus 
                    autocomplete="name" 
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-base-content font-semibold mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                    <x-ui.icon name="envelope" size="md" />
                </div>
                <x-text-input 
                    id="email" 
                    class="input input-bordered w-full pl-12 h-12 bg-base-100 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    placeholder="mario@esempio.com"
                    required 
                    autocomplete="username" 
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-base-content font-semibold mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                    <x-ui.icon name="lock" size="md" />
                </div>
                <x-text-input 
                    id="password" 
                    class="input input-bordered w-full pl-12 h-12 bg-base-100 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20"
                    type="password"
                    name="password"
                    placeholder="Minimo 8 caratteri"
                    required 
                    autocomplete="new-password" 
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Conferma Password')" class="text-base-content font-semibold mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/40">
                    <x-ui.icon name="lock" size="md" />
                </div>
                <x-text-input 
                    id="password_confirmation" 
                    class="input input-bordered w-full pl-12 h-12 bg-base-100 border-base-300 focus:border-primary focus:ring-2 focus:ring-primary/20"
                    type="password"
                    name="password_confirmation" 
                    placeholder="Ripeti la password"
                    required 
                    autocomplete="new-password" 
                />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-full h-12 text-base gap-2 shadow-lg hover:shadow-xl transition-all">
            <x-ui.icon name="user-plus" size="md" />
            {{ __('Crea Account') }}
        </button>

        <!-- Divider -->
        <div class="divider text-base-content/40">oppure</div>

        <!-- Login Link -->
        <div class="text-center">
            <span class="text-base-content/60">Hai già un account?</span>
            <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 font-semibold ml-1 transition-colors">
                Accedi
            </a>
        </div>
    </form>
</x-guest-layout>
