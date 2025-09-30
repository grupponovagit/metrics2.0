<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Grid\Admin\UserGrid;
use BalajiDharma\LaravelAdminCore\Actions\User\UserCreateAction;
use BalajiDharma\LaravelAdminCore\Actions\User\UserUpdateAction;
use BalajiDharma\LaravelAdminCore\Data\User\UserCreateData;
use BalajiDharma\LaravelAdminCore\Data\User\UserUpdateData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;


class UserController extends Controller
{
    /**
     * Get the details of the currently authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the details of the currently authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userDetails(Request $request)
    {
        $user = Auth::user();  // Recupera l'utente autenticato

        if ($user) {
            // Costruisci l'array dei dati manualmente
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'photo_url' => $user->photo_url,
                'provider' => $user->provider,
                // campi rimossi: email_verified_at, codice_fiscale
                'phone' => $user->phone,
                'privacy' => $user->privacy,
                'cessione_dati' => $user->cessione_dati,
                'marketing' => $user->marketing,
                'cluster' => $user->cluster,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
                'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : null,
                'profile_complete' => $user->profile_complete,
                'phone_verified' => $user->phone_verified

            ];

            return response()->json([
                'success' => true,
                'message' => 'User details retrieved successfully.',
                'data' => $userData
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No authenticated user found.'
        ], 404);
    }

    public function logOutUser(Request $request)
    {
        try {
            // Revoca il token attualmente in uso
            $token = $request->user()->currentAccessToken();
            $token->delete(); // Elimina il token corrente

            // Opzionale: Revoca tutti gli altri token dell'utente
            // Questo passaggio assicura che tutti i token associati all'utente vengano revocati.
            $request->user()->tokens()->where('id', '!=', $token->id)->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize('adminViewAny', User::class);
        $users = (new User)->newQuery()->with(['roles']);

        $crud = (new UserGrid)->list($users);
        return view('admin.crud.index', compact('crud'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('adminCreate', User::class);
        $crud = (new UserGrid)->form();
        return view('admin.crud.edit', compact('crud'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserCreateData $data, UserCreateAction $userCreateAction)
    {
        $this->authorize('adminCreate', User::class);
        $userCreateAction->handle($data);

        return redirect()->route('admin.user.index')
            ->with('message', __('User created successfully.'));
    }
    public function storeAPI(Request $request)
    {
        // Valida i dati del form
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255', // Validazione per il cognome
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // Conferma per il campo password
            // campi rimossi
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|max:255',
        ]);

        // Crea un nuovo utente
        $user = User::create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')), // Hash della password
            // campi rimossi
            'phone' => $request->input('phone'),
            'role' => $request->input('role'),
        ]);

        // Restituisci una risposta di successo
        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        $this->authorize('adminView', $user);
        $crud = (new UserGrid)->show($user);
        return view('admin.crud.show', compact('crud'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('adminUpdate', $user);

        $crud = (new UserGrid)->form($user);
        return view('admin.crud.edit', compact('crud'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserUpdateData $data, User $user, UserUpdateAction $userUpdateAction)
    {
        $this->authorize('adminUpdate', $user);
        $userUpdateAction->handle($data, $user);

        return redirect()->route('admin.user.index')
            ->with('message', __('User updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->authorize('adminDelete', $user);
        $user->delete();

        return redirect()->route('admin.user.index')
            ->with('message', __('User deleted successfully'));
    }

    /**
     * Show the user a form to change their personal information & password.
     *
     * @return \Illuminate\View\View
     */
    public function accountInfo()
    {
        $user = Auth::user();

        return view('admin.user.account_info', compact('user'));
    }

    /**
     * Save the modified personal information for a user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accountInfoStore(Request $request)
    {
        $request->validateWithBag('account', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::user()->id],
        ]);

        /** @var User|null $user */
        $user = Auth::user();
        if (!($user instanceof User)) {
            return redirect()->route('admin.account.info')->with('account_message', __('Utente non autenticato.'));
        }
        $user->fill($request->except(['_token']));
        $saved = $user->save();

        if ($saved) {
            $message = 'Account aggiornato con successo.';
        } else {
            $message = 'Errore durante il salvataggio. Per favore, riprova.';
        }

        return redirect()->route('admin.account.info')->with('account_message', __($message));
    }

    /**
     * Save the new password for a user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePasswordStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', Rules\Password::defaults()],
            'confirm_password' => ['required', 'same:new_password', Rules\Password::defaults()],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->failed()) {
                return;
            }
            if (! Hash::check($request->input('old_password'), Auth::user()->password)) {
                $validator->errors()->add(
                    'old_password',
                    __('Vecchia password errata.')
                );
            }
        });

        $validator->validateWithBag('password');

        /** @var User|null $user */
        $user = Auth::user();
        if (!($user instanceof User)) {
            return redirect()->route('admin.account.info')->with('password_message', __('Utente non autenticato.'));
        }
        $user->password = Hash::make($request->input('new_password'));
        $saved = $user->save();

        if ($saved) {
            $message = 'Password aggiornata con successo.';
        } else {
            $message = 'Errore durante il salvataggio. Per favore, riprova.';
        }

        return redirect()->route('admin.account.info')->with('password_message', __($message));
    }
}
