<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-warning to-error rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl">
            <x-ui.icon name="shield" size="xl" class="text-white" />
        </div>
        <h2 class="text-3xl font-bold text-base-content mb-2">Area Protetta</h2>
        <p class="text-base-content/60">Conferma la tua password per continuare</p>
    </div>

    <div class="card bg-base-200 border border-base-300/50 mb-6">
        <div class="card-body p-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-warning/10 rounded-lg flex items-center justify-center text-warning flex-shrink-0">
                    <x-ui.icon name="exclamation" size="md" />
                </div>
                <p class="text-base-content/70 leading-relaxed">
                    {{ __('Questa è un\'area protetta dell\'applicazione. Si prega di confermare la password prima di continuare.') }}
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

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
                    placeholder="Inserisci la tua password"
                    required 
                    autocomplete="current-password" 
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-full h-12 text-base gap-2 shadow-lg hover:shadow-xl transition-all">
            <x-ui.icon name="check" size="md" />
            {{ __('Conferma') }}
        </button>
    </form>
</x-guest-layout>
