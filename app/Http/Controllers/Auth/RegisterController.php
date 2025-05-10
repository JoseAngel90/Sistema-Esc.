<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PendingUser; // Importar el modelo
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\NewUserRegistered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $pendingUser = PendingUser::where('email', $data['email'])->first();

        if ($pendingUser) {
            return $pendingUser; // Si ya existe, devuelve el registro existente
        }

        return PendingUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Handle a registered user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function registered(Request $request, $user)
    {
        $pendingUser = PendingUser::where('email', $user->email)->first();

        if ($pendingUser->wasRecentlyCreated) {
            return redirect()->route('login')->with('success', 'Tu registro ha sido exitoso. Por favor, espera a que un administrador apruebe tu cuenta.');
        }

        return redirect()->route('login')->with('success', 'Tu registro ya estaba pendiente de aprobación. Por favor, espera a que un administrador lo revise.');
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // Crear el usuario pendiente
        $user = $this->create($request->all());

        // Redirigir con un mensaje de éxito
        return $request->wantsJson()
                    ? new JsonResponse(['message' => 'Registro exitoso. Por favor, espera aprobación.'], 201)
                    : redirect()->route('login')->with('success', 'Tu registro ha sido exitoso. Por favor, espera a que un administrador apruebe tu cuenta.');
    }
}
