<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampaniaCanal extends Model {
    protected $table = 'campania_canal';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'campania_id',
        'canal_id',
        // 'status',
        // 'correo',
        'usuario_ingresa_id',
    ];



    public function Campania(){
        return $this->belongsTo('App\Models\Campania','campania_id');
    }

    public function Canal(){
        return $this->belongsTo('App\Models\Canal','canal_id');
    }
}