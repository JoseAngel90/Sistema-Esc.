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
        'etiqueta_rubro',       
        'etiqueta_pestania',
        'etiqueta_nombre',
        'etiqueta_aspecto5',
        'etiqueta_aspecto4',
        'etiqueta_aspecto3',
        'etiqueta_aspecto2',
        'etiqueta_aspecto1',
    ];

    // Relación inversa con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
