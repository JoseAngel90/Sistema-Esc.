<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Ruy hernandez',
            'email' => 'admin@colegiodigital.org',
            'password' => Hash::make('Admin.-01'), 
            'roles' => 'admin', // Asignar el rol de administrador
        ]);

        User::create([
        'name' => 'Jose Angel Cuahutle',
        'email' => 'josexochitemol8363@gmail.com',
        'password' => Hash::make('Admin.-02'),
        'roles' => 'admin',
    ]);
    }
}
