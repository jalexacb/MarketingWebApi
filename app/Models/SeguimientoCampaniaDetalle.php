<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SeguimientoCampaniaDetalle extends Model {
    protected $table = 'seguimiento_campania_detalle';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'seguimiento_campania_id',
        'campania_canal_id',
        'campania_contacto_id',
        'estado_mensaje',
        
        'usuario_ingresa_id',
    ];

    public function Campania(){
        return $this->belongsTo('App\Models\Campania');
    }
}