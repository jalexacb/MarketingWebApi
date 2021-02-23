<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model {
    protected $table = 'rol';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'usuario_ingresa_id',
    ];

    public function Usuario(){
        return $this->belongsTo('App\Models\Usuario','usuario_id');
    }
}