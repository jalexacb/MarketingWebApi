<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SeguimientoCampania extends Model {
    protected $table = 'seguimiento_campania';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'fecha_inicio_seguimiento',
        'fecha_fin_seguimiento',
        'mensajes_leidos',
        'mensajes_enviados',
        'mensajes_entregados',
        'mensajes_leidos',
        'mensajes_rebotados',
        'mensajes_respondidos',
        'campania_id',
        'usuario_ingresa_id',
    ];

    public function Campania(){
        return $this->belongsTo('App\Models\Campania');
    }
}