<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampaniaInteres extends Model {
    protected $table = 'campania_interes';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'campania_id',
        'interes_id',
        // 'status',
        // 'correo',
        'usuario_ingresa_id',
    ];



    public function Campania(){
        return $this->belongsTo('App\Models\Campania','campania_id');
    }

    public function Interes(){
        return $this->belongsTo('App\Models\Interes','interes_id');
    }
}