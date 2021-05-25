<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model {
    protected $table = 'parametro';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'valor',
        // 'usuario_id',
    ];

    // public function Usuario(){
    //     return $this->belongsTo('App\Models\Usuario','usuario_id');
    // }
}