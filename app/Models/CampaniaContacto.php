<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampaniaContacto extends Model {
    protected $table = 'campania_contacto';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'campania_id',
        'contacto_id',
        // 'status',
        // 'correo',
        'usuario_ingresa_id',
    ];



    public function Campania(){
        return $this->belongsTo('App\Models\Campania','campania_id');
    }

    public function Contacto(){
        return $this->belongsTo('App\Models\Contacto','contacto_id');
    }
}