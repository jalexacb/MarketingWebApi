<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContactoInteres extends Model {
    protected $table = 'contacto_interes';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'contacto_id',
        'interes_id',
        // 'status',
        // 'correo',
        'usuario_ingresa_id',
    ];



    public function Contacto(){
        return $this->belongsTo('App\Models\Contacto','contacto_id');
    }

    public function Interes(){
        return $this->belongsTo('App\Models\Interes','interes_id');
    }
}