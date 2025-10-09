<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-info to-success rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl">
            <x-ui.icon name="envelope" size="xl" class="text-white" />
        </div>
        <h2 class="text-3xl font-bold text-base-content mb-2">Verifica Email</h2>
        <p class="text-base-content/60">Controlla la tua casella di posta</p>
    </div>

    <div class="card bg-base-200 border border-base-300/50">
        <div class="card-body p-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-info/10 rounded-lg flex items-center justify-center text-info flex-shrink-0">
                    <x-ui.icon name="info" size="md" />
                </div>
                <p class="text-base-content/70 leading-relaxed">
                    {{ __('Grazie per esserti registrato! Prima di iniziare, potresti verificare il tuo indirizzo email cliccando sul link che ti abbiamo appena inviato? Se non hai ricevuto l\'email, saremo lieti di inviartene un\'altra.') }}
                </p>
            </div>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mt-6">
            <x-ui.icon name="check-circle" size="md" />
            <span>{{ __('Un nuovo link di verifica è stato inviato all\'indirizzo email che hai fornito durante la registrazione.') }}</span>
        </div>
    @endif

    <div class="flex items-center justify-between gap-4 mt-8">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <button type="submit" class="btn btn-primary w-full gap-2">
                <x-ui.icon name="envelope" size="md" />
                {{ __('Reinvia Email di Verifica') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost btn-outline gap-2">
                <x-ui.icon name="arrow-right" size="md" />
                {{ __('Logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>
