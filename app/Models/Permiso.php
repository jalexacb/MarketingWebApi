<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model {
    protected $table = 'permiso';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'rol_id',
        'usuario_id',
        'menu_id',
        'ver',
        'crear',
        'editar',
        'eliminar',
        'usuario_ingresa_id',
    ];

    public function Usuario(){
        return $this->belongsTo('App\Models\Usuario','usuario_id');
    }

    public function Menu(){
        return $this->belongsTo('App\Models\Menu','menu_id');
    }
    

}