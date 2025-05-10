<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\DatosGenerales; // Importación de DatosGenerales
use App\Models\Alumno; // Importación de Alumno
use App\Models\Grupo; // Importación de Grupo

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con DatosGeneralesEscuela
    public function datosGeneralesEscuela()
    {
        return $this->hasOne(DatosGenerales::class, 'user_id', 'id');
    }

    /**
     * Relación con Alumnos (uno a muchos).
     */
    // Relación con Alumnos
    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'user_id'); // 'user_id' debe coincidir con la columna en la tabla de alumnos
    }

    /**
     * Relación con Grupos (uno a muchos).
     */
    // Relación con Grupos
    public function grupos()
{
    return $this->hasMany(Grupo::class);
}


}
