<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtiquetaRubro extends Model
{
    use HasFactory;

    protected $table = 'etiquetas_rubros';

    protected $fillable = [
        'user_id',
        'grado',
        'grupo',
        'tipo',       // ejemplo: 'rubro1', 'evaluacion3'
        'etiqueta'    // ejemplo: 'Responsabilidad'
    ];

    // Relación inversa con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
