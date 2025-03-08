<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    /**
     * Relación con el modelo User (pertenece a un usuario).
     * Esta es la relación inversa de la relación hasMany en el modelo User.
     */

    public function user()
    {
        return $this->belongsTo(User::class); // Define que pertenece a un usuario con la clave foránea 'user_id'
    }
    
   
    public function getNombreCompletoAttribute()
    {
        return "{$this->grado} {$this->grupo}";
    }

    // Define los atributos que son asignables masivamente
    protected $table = 'grupos';
    protected $fillable = [
        'grado',
        'grupo',
        'periodo',
        'user_id',
    ];

    
}
