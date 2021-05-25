<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model {
    protected $table = 'contacto';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'nombres',
        'apellidos',
        'celular',
        'correo',
        'usuario_ingresa_id',
    ];

    public function contactointereses(){
        return $this->hasMany('App\Models\ContactoInteres');
    }
}