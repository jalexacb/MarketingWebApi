<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Interes extends Model {
    protected $table = 'interes';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'usuario_ingresa_id',
    ];
}