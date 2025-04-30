<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No se encontró un usuario con ese correo electrónico.']);
        }

        $newPassword = Str::random(8);
        DB::table('users')->where('email', $request->email)->update(['password' => Hash::make($newPassword)]);

        Mail::send('emails.password', ['password' => $newPassword], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Nueva contraseña');
        });

        return back()->with('status', 'Se ha enviado una nueva contraseña a su correo electrónico.');
    }
}