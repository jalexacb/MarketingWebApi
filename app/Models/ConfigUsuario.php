<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConfigUsuario extends Model {
    protected $table = 'configusuario';
    public $timestamps = false;
    protected $fillable = [
        'token',
        'intento_login',
        'fecha_bloqueado',
        'usuario_id',
        'usuario_ingresa_id',
    ];

    public function Usuario(){
        return $this->belongsTo('App\Models\Usuario','usuario_id');
    }
}