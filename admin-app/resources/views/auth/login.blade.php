<x-guest-layout>
    {{-- Header con Logo --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-primary to-secondary rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl">
            <x-ui.icon name="arrow-right" size="xl" class="text-white" />
        </div>
        <h2 class="text-3xl font-bold text-base-content mb-2">Bentornato!</h2>
        <p class="text-base-content/60">Accedi al tuo account per continuare</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
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
                    placeholder="tu@esempio.com"
                    required 
                    autofocus 
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
                    placeholder="••••••••"
                    required 
                    autocomplete="current-password" 
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center cursor-pointer group">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="checkbox checkbox-primary checkbox-sm" 
                    name="remember"
                >
                <span class="ml-2 text-sm text-base-content/70 group-hover:text-base-content transition-colors">
                    {{ __('Ricordami') }}
                </span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary hover:text-primary/80 font-semibold transition-colors" href="{{ route('password.request') }}">
                    {{ __('Password dimenticata?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-full h-12 text-base gap-2 shadow-lg hover:shadow-xl transition-all">
            <x-ui.icon name="arrow-right" size="md" />
            {{ __('Accedi') }}
        </button>

        <!-- Info -->
        <div class="text-center mt-6">
            <div class="text-sm text-base-content/60">
                <x-ui.icon name="info" size="sm" class="inline" />
                Gli account vengono creati dal team IT
            </div>
        </div>
    </form>
</x-guest-layout>
