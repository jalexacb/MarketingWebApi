<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampaniaObjetivo extends Model {
    protected $table = 'campania_objetivo';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'campania_id',
        'objetivo_id',
        // 'status',
        // 'correo',
        'usuario_ingresa_id',
    ];



    public function Campania(){
        return $this->belongsTo('App\Models\Campania','campania_id');
    }

    public function Objetivo(){
        return $this->belongsTo('App\Models\Objetivo','objetivo_id');
    }
}