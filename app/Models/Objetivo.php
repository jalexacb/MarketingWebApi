<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model {
    protected $table = 'objetivo';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'usuario_ingresa_id',
    ];
}