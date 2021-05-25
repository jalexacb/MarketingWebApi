<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model {
    protected $table = 'menu';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'title',
        'type',
        'icon',
        'padre_id',
        'url',
        'target',
        'breadcrumbs',
        'status',
    ];

    protected $hidden = [
        // 'padre_id',
    ];

    public function Children(){
        $menu = $this->hasMany('App\Models\Menu','padre_id')->where('status','A')->with('children');
        if(isset($_GET['usuario_id'])){
            $menu->whereHas('permiso', function($q)
            {
                $q->where('permiso.status', 'A')->where('permiso.usuario_id',$_GET['usuario_id']);//estoy probando con un valor estático temporalmente
    
            });
            $menu->with(['permiso' => function ($q){
                $q->where('permiso.status', 'A');
                
                    $q->where('permiso.usuario_id',$_GET['usuario_id']);//estoy probando con un valor estático temporalmente
            
                   
                
            }]);
        }else if(isset($_GET['rol_id'])){
            $menu->whereHas('permiso', function($q)
            {
                $q->where('permiso.status', 'A')->where('permiso.rol_id',$_GET['rol_id']);//estoy probando con un valor estático temporalmente
    
            });

            $menu->with(['permiso' => function ($q){
                $q->where('permiso.status', 'A');
                
                $q->where('permiso.rol_id',$_GET['rol_id']);//estoy probando con un valor estático temporalmente
            
                   

            }]);
            
        }

    
           

        return $menu;
    }

    public function Permiso(){
        return $this->hasOne('App\Models\Permiso')->where('status','A');
    }
}