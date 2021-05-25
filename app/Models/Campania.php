<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Campania extends Model {
    protected $table = 'campania';
    public $timestamps = false;
    protected $fillable = [
        'id',
        // 'identificacion',
        // 'razon_social',
        // 'nombre_comercial',
        'nombre',
        'descripcion',
        'mensaje',
        'tipo',
        'url',
        'url_media',
        'usuario_ingresa_id',
    ];

    public function Campania_intereses(){
        return $this->hasMany('App\Models\CampaniaInteres')->with(['interes' => function ($q){
            $q->where('interes.status', 'A');
        }]);
    }
    public function Campania_canales(){
        return $this->hasMany('App\Models\CampaniaCanal')->with(['canal' => function ($q){
            $q->where('canal.status', 'A');
        }]);
    }
    public function Campania_contactos(){
        return $this->hasMany('App\Models\CampaniaContacto')->with(['contacto' => function ($q){
            $q->where('contacto.status', 'A');
        }]);
    }
    public function Campania_Objetivos(){
        return $this->hasMany('App\Models\CampaniaObjetivo')->with(['objetivo' => function ($q){
            $q->where('objetivo.status', 'A');
        }]);
    }
}