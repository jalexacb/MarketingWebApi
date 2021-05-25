<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller {

    public function index() {
        try {
            //code...
            $menus = Menu::where('status','A')->where('padre_id',null);
            
            $menus = $menus->with('children')->get();
            return response()->json($menus, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function indexByPermisos() {
        try {
            //code...
            $menus = Menu::where('menu.status','A')->where('padre_id',null)->whereHas('permiso', function($q)
            {
                $q->where('permiso.status', 'A');
                if(isset($_GET['usuario_id'])){
                    $q->where('permiso.usuario_id',$_GET['usuario_id']);//estoy probando con un valor estático temporalmente
                }else if(isset($_GET['rol_id'])){
                    $q->where('permiso.rol_id',$_GET['rol_id']);//estoy probando con un valor estático temporalmente
                }
                

            });
            $menus->with(['permiso' => function ($q){
                $q->where('permiso.status', 'A');
                if(isset($_GET['usuario_id'])){
                    $q->where('permiso.usuario_id',$_GET['usuario_id']);//estoy probando con un valor estático temporalmente
                }else if(isset($_GET['rol_id'])){
                    $q->where('permiso.rol_id',$_GET['rol_id']);//estoy probando con un valor estático temporalmente
                }
            }]);
            // $menus = Menu


            $menus = $menus->with('children')->get();
            return response()->json($menus, 200); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message"=>$e->getMessage()]); 
        }
        
    }

    public function indexAll() {
        try {
            //code...
            $menus = Menu::where('status','A')->where('padre_id',null);
            
            $menus = $menus->with('children')->get()->toArray();
            $menusAll = [];
            foreach ($menus as $key => $value) {
                # code...
                if($value['padre_id']==null){
                    array_push($menusAll, $value);
                }
                
                    foreach ($value['children'] as $key => $children) {
                        # code...
                        // $hijo = children
                        // if()
                        array_push($menusAll, $children);
                        foreach ($children['children'] as $key => $hijo) {
                            # code...
                            array_push($menusAll, $hijo);
                        }
                    }
                    
                
            }

            for ($i=0; $i < sizeof($menusAll); $i++) { 
                # code...
                unset($menusAll[$i]['children']);
            }
                # code...
               
            
            
            return response()->json($menusAll, 200); 
        } catch (\Exception $e) {
            //throw $th;
        }
        
    }

    public function delete($id){
        try {
            $rol = Rol::find($id);
            $rol->status = 'I';
            $rol->update();

            return response()->json($rol, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request){
        try {
            $input = $request->all();
            $rol = Rol::create($input);
            $data = [
                'status'    => 'success',
                'code'      => 201,
                'message'   => 'Rol creado correctamente.',  
                'usuario'   => $rol,   
            ];

            return response()->json($rol, 200); 
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
