<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Canal extends Model {
    protected $table = 'canal';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'usuario_ingresa_id',
    ];
}