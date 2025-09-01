<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ModuleAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Determina il redirect intelligente in base ai permessi dell'utente
        $user = Auth::user();
        $accessibleModules = ModuleAccessService::getAccessibleModules();
        
        // Se l'utente ha accesso a moduli, vai al primo modulo disponibile
        if (count($accessibleModules) > 0) {
            $firstModule = array_values($accessibleModules)[0];
            return redirect()->intended($firstModule['url']);
        }
        
        // Altrimenti vai al dashboard admin generico
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
