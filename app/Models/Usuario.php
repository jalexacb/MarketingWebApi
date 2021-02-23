<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model {
    protected $table = 'usuario';
    public $timestamps = false;
    protected $fillable = [
        'usuario',
        'password',
        'nombres',
        'apellidos',
        'nacionalidad',
        'fecha_nacimiento',
        'sexo',
        'path_log',
        'empresa_id',
        'rol_id',
        'usuario_ingresa_id'
    ];
    
    protected $hidden = [
        'password','usuario_ingresa_id'
    ];

    public function Rol(){
        return $this->belongsTo('App\Models\Rol','rol_id');
    }

    public function Empresa(){
        return $this->belongsTo('App\Models\Empresa','empresa_id');
    }
}