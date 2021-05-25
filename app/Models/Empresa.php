<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model {
    protected $table = 'empresa';
    public $timestamps = false;
    protected $fillable = [
        'identificacion',
        'razon_social',
        'nombre_comercial',
        'correo',
        'celular',
        'direccion',
        'imagen_logo',
        'tipo_identificacion',
    ];

    public function Usuarios(){
        return $this->hasMany('App\Models\Usuario');
    }
}