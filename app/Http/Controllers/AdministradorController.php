<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdministradorController extends Controller {
    
   public function index()
    {
        // Obtener todos los usuarios desde la base de datos
        $users = User::all();
        $pendingUsers = PendingUser::all(); // Obtener usuarios pendientes

        // Pasar los usuarios a la vista
        return view('Administrador', compact('users', 'pendingUsers'));
    }


    

   public function destroy($id)
{
    // Evitar que un usuario se elimine a sí mismo
    if ($id == auth()->user()->id) {
        return redirect()->route('administrador')->with('error', 'No puedes eliminar tu propio usuario.');
    }

    $user = User::findOrFail($id);

    if ($user->delete()) {
        return redirect()->route('administrador')->with('success', 'Usuario eliminado correctamente.');
    } else {
        return redirect()->route('administrador')->with('error', 'Error al eliminar el usuario.');
    }
}

public function approve($id)
{
    $pendingUser = PendingUser::findOrFail($id);

    // Verificar si el correo ya existe en la tabla `users`
    if (User::where('email', $pendingUser->email)->exists()) {
        return redirect()->route('administrador')->with('error', 'El correo ya está registrado en el sistema.');
    }

    // Crear el usuario en la tabla `users`
    User::create([
        'name' => $pendingUser->name,
        'email' => $pendingUser->email,
        'password' => $pendingUser->password,
    ]);

    // Eliminar el usuario de la tabla `pending_users`
    $pendingUser->delete();

    return redirect()->route('administrador')->with('success', 'Usuario aprobado correctamente.');
}

public function resetPassword($id)
{
    $user = User::findOrFail($id);

    // Generar una nueva contraseña temporal
    $newPassword = Str::random(8);

    // Actualizar la contraseña en la base de datos
    $user->password = Hash::make($newPassword);
    $user->save();

    // Guardar la nueva contraseña en la sesión
    return redirect()->route('administrador')->with([
        'success' => "La contraseña ha sido restablecida para el usuario: " . $user->email,
        'newPassword' => $newPassword,
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'roles' => 'admin', // Asignar el rol de administrador
    ]);

    return redirect()->route('administrador')->with('success', 'Administrador registrado correctamente.');
}

}


?>