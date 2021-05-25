<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EventoCampania extends Model {
    protected $table = 'evento_campania';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'fecha_inicio',
        'fecha_fin',
        'status',
        // 'mensajes_leidos',
        'campania_id',
        'usuario_ingresa_id',
    ];

    public function Campania(){
        return $this->belongsTo('App\Models\Campania');
    }
}