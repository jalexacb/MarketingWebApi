<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model {
    protected $table = 'registro';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'tipo',
        "nombre",
        'menu_id',
        'usuario_ingresa_id',
    ];

    public function Menu(){
        return $this->belongsTo('App\Models\Menu');
    } 
    public function Usuario(){
        return $this->belongsTo('App\Models\Usuario','usuario_ingresa_id');
    } 
}